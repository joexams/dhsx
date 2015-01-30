using System;
using System.Collections.Generic;

namespace ProtoSpec
{
    public class ProtoSpecParser
    {
        public ProtoSpecParser(string code, int lineOffset)
        {
            Code = code;

            LineOffset = lineOffset;

            Scanner = new ProtoSpecScanner(code);
        }

        private string Code
        {
            get;
            set;
        }

        private int LineOffset
        {
            get;
            set;
        }

        private ProtoSpecScanner Scanner
        {
            get;
            set;
        }

        private ProtoSpecToken CurrentToken
        {
            get;
            set;
        }

        private int EnumValue
        {
            get;
            set;
        }

        private Dictionary<string, ProtoSpecEnumValue> EnumValueDict
        {
            get;
            set;
        }

        private string CurrentModule
        {
            get;
            set;
        }


        private ProtoSpecToken NextToken()
        {
            CurrentToken = Scanner.NextToken();

            return CurrentToken;
        }

        public void ResetEnumContext()
        {
            EnumValue = 0;

            EnumValueDict = new Dictionary<string, ProtoSpecEnumValue>(StringComparer.OrdinalIgnoreCase);
        }

        public ProtoSpecEnumValue SetEnumValue(string name, int value)
        {
            if (EnumValueDict.ContainsKey(name))
                Error("枚举值" + name + "重复定义");

            ProtoSpecEnumValue result = new ProtoSpecEnumValue(name, value);

            EnumValueDict.Add(name, result);

            return result;
        }

        public ProtoSpecEnumValue AllocEnumValue(string name)
        {
            if (EnumValueDict.ContainsKey(name))
                return EnumValueDict[name];

            if (EnumValue > 255)
                Error("枚举值自动增长超过了255");

            ProtoSpecEnumValue value = new ProtoSpecEnumValue(name, EnumValue);

            EnumValueDict.Add(name, value);

            EnumValue = EnumValue + 1;

            return value;
        }

        public void ExportEnumValues(ProtoSpecEnumValueList list)
        {
            foreach (KeyValuePair<string, ProtoSpecEnumValue> pair in EnumValueDict)
            {
                list.Add(pair.Value);
            }
        }

        public ProtoSpecDocument Parse()
        {
            ProtoSpecDocument document = new ProtoSpecDocument();

            ParseModules(document);

            return document;
        }

        private void ParseModules(ProtoSpecDocument document)
        {
            while (true)
            {
                ResetEnumContext();

                NextToken();

                if (CurrentToken.Type != ProtoSpecTokenType.Identifier)
                {
                    if (CurrentToken.Type != ProtoSpecTokenType.EndOfFile)
                    {
                        Error("缺少模块名");
                    }

                    break;
                }

                string name = CurrentToken.Text;

                CurrentModule = name;

                NextToken();

                if (CurrentToken.Type != ProtoSpecTokenType.Equal)
                {
                    Error("模块名后缺少'='");
                    break;
                }

                NextToken();

                if (CurrentToken.Type != ProtoSpecTokenType.Numeral)
                {
                    Error("模块ID不是有效数值");
                    break;
                }

                string moduleId = CurrentToken.Text;

                ProtoSpecModule module = new ProtoSpecModule(name, moduleId);

                NextToken();

                if (CurrentToken.Type != ProtoSpecTokenType.LeftBrace)
                {
                    Error("模块主体起始位置，缺少'{'");
                    break;
                }

                ParseModuleBody(module);

                if (CurrentToken.Type != ProtoSpecTokenType.RightBrace)
                {
                    Error("模块主体结束位置，缺少'}'");
                    break;
                }

                ExportEnumValues(module.EnumValues);

                document.Modules.Add(module);
            }
        }

        private void ParseModuleBody(ProtoSpecModule module)
        {
            while (true)
            {
                NextToken();

                if (CurrentToken.Type == ProtoSpecTokenType.Class)
                {
                    ProtoSpecClassDef classDef = ParseClass(module);

                    if (classDef == null)
                        break;

                    module.Classes.Add(classDef);
                }
                else if (CurrentToken.Type == ProtoSpecTokenType.Identifier)
                {
                    ProtoSpecAction action = ParseAction(module);

                    if (action == null)
                        break;

                    module.Actions.Add(action);
                }
                else if (CurrentToken.Type == ProtoSpecTokenType.EndOfFile ||
                         CurrentToken.Type == ProtoSpecTokenType.RightBrace)
                {
                    break;
                }
                else
                {
                    Error("未知的标识符");
                    break;
                }
            }
        }

        private ProtoSpecClassDef ParseClass(ProtoSpecModule module)
        {
            NextToken();

            if (CurrentToken.Type != ProtoSpecTokenType.Identifier)
            {
                Error("缺少类名");
                return null;
            }

            string name = CurrentToken.Text;

            string basePart1 = null;
            string basePart2 = null;

            NextToken();

            if (CurrentToken.Type == ProtoSpecTokenType.Colon)
            {
                NextToken();

                if (CurrentToken.Type != ProtoSpecTokenType.Identifier)
                {
                    Error("缺少基类名称");
                    return null;
                }

                basePart1 = CurrentToken.Text;

                NextToken();

                if (CurrentToken.Type == ProtoSpecTokenType.Dot)
                {
                    NextToken();

                    if (CurrentToken.Type != ProtoSpecTokenType.Identifier)
                    {
                        Error("缺少基类名称");
                        return null;
                    }

                    basePart2 = CurrentToken.Text;

                    NextToken();
                }
            }

            ProtoSpecSubset classBody = ParseSpecSubset(null);

            if (basePart1 == null)
                return new ProtoSpecClassDef(name, null, null, classBody);
            else if (basePart2 == null)
                return new ProtoSpecClassDef(name, null, basePart1, classBody);
            else
                return new ProtoSpecClassDef(name, basePart1, basePart2, classBody);
        }

        private ProtoSpecAction ParseAction(ProtoSpecModule module)
        {
            string name = CurrentToken.Text;

            NextToken();

            if (CurrentToken.Type != ProtoSpecTokenType.Equal)
            {
                Error("接口名'" + name + "'后缺少'='");
                return null;
            }

            NextToken();

            if (CurrentToken.Type != ProtoSpecTokenType.Numeral)
            {
                Error("接口'" + name + "'的ID不是有效数值");
                return null;
            }

            string actionId = CurrentToken.Text;

            NextToken();

            if (CurrentToken.Type != ProtoSpecTokenType.LeftBrace)
            {
                Error("操作'" + name + "'的主体起始位置，缺少'{'");
                return null;
            }

            NextToken();

            if (CurrentToken.Type != ProtoSpecTokenType.In)
            {
                Error("接口'" + name + "'的主体，缺少'in'关键字");
                return null;
            }

            ProtoSpecAction action = new ProtoSpecAction(name, actionId);

            NextToken();

            ProtoSpecSubset input = ParseSpecSubset(action);

            NextToken();

            if (CurrentToken.Type != ProtoSpecTokenType.Out)
            {
                Error("接口'" + name + "'的主体，缺少'out'关键字");
                return null;
            }

            NextToken();

            ProtoSpecSubset output = ParseSpecSubset(action);

            NextToken();

            if (CurrentToken.Type != ProtoSpecTokenType.RightBrace)
            {
                Error("接口'" + name + "'的主体结束位置，缺少'}'");
                return null;
            }

            action.Input = input;
            action.Output = output;

            return action;
        }

        private ProtoSpecSubset ParseSpecSubset(ProtoSpecAction action)
        {
            if (CurrentToken.Type != ProtoSpecTokenType.LeftBrace)
            {
                Error("规格定义起始位置，缺少'{'");
            }

            ProtoSpecSubset subset = new ProtoSpecSubset(action);

            while (true)
            {
                NextToken();

                if (CurrentToken.Type != ProtoSpecTokenType.Identifier)
                {
                    if (CurrentToken.Type != ProtoSpecTokenType.EndOfFile && CurrentToken.Type != ProtoSpecTokenType.RightBrace)
                    {
                        Error("缺少列名");
                    }

                    break;
                }

                string name = CurrentToken.Text;

                NextToken();

                if (CurrentToken.Type != ProtoSpecTokenType.Colon)
                {
                    Error("列名之后缺少':'");
                    break;
                }

                NextToken();

                if (IsColumnType(CurrentToken.Type) == false)
                {
                    Error("缺少类型定义");
                    break;
                }

                ProtoSpecColumnType type = (ProtoSpecColumnType)CurrentToken.Type;

                ProtoSpecColumn column = null;

                if (CurrentToken.Type == ProtoSpecTokenType.List)
                {
                    NextToken();

                    if (CurrentToken.Type == ProtoSpecTokenType.LeftAngular)
                    {
                        NextToken();

                        if (CurrentToken.Type != ProtoSpecTokenType.Identifier)
                        {
                            Error("列表类型缺少类型名");
                            break;
                        }

                        string part1 = CurrentToken.Text;

                        string part2 = null;

                        NextToken();

                        if (CurrentToken.Type == ProtoSpecTokenType.Dot)
                        {
                            NextToken();

                            if (CurrentToken.Type != ProtoSpecTokenType.Identifier)
                            {
                                Error("列表类型的模块名后缺少类型名");
                                break;
                            }

                            part2 = CurrentToken.Text;

                            NextToken();
                        }

                        if (CurrentToken.Type != ProtoSpecTokenType.RightAngular)
                        {
                            Error("列表类型后缺少闭合符号'>'");
                            break;
                        }

                        if (part2 == null)
                            column = new ProtoSpecColumn(name, type, part1);
                        else
                            column = new ProtoSpecColumn(name, type, part1, part2);
                    }
                    else
                    {
                        ProtoSpecSubset format = ParseSpecSubset(action);

                        column = new ProtoSpecColumn(name, type, format);
                    }
                }
                else if (CurrentToken.Type == ProtoSpecTokenType.TypeOf)
                {
                    NextToken();

                    if (CurrentToken.Type != ProtoSpecTokenType.LeftAngular)
                    {
                        Error("自定义类型前缺少起始符号'<'");
                        break;
                    }

                    NextToken();

                    if (CurrentToken.Type != ProtoSpecTokenType.Identifier)
                    {
                        Error("自定义类型的模块名后缺少类型名");
                        break;
                    }

                    string part1 = CurrentToken.Text;

                    string part2 = null;

                    NextToken();

                    if (CurrentToken.Type == ProtoSpecTokenType.Dot)
                    {
                        NextToken();

                        if (CurrentToken.Type != ProtoSpecTokenType.Identifier)
                        {
                            Error("自定义类型的模块名后缺少类型名");
                            break;
                        }

                        part2 = CurrentToken.Text;

                        NextToken();
                    }

                    if (CurrentToken.Type != ProtoSpecTokenType.RightAngular)
                    {
                        Error("自定义类型后缺少闭合符号'>'");
                        break;
                    }

                    if (part2 == null)
                        column = new ProtoSpecColumn(name, type, part1);
                    else
                        column = new ProtoSpecColumn(name, type, part1, part2);
                }
                else if (CurrentToken.Type == ProtoSpecTokenType.Enum)
                {
                    ProtoSpecEnumValueList values = ParseEnum();

                    column = new ProtoSpecColumn(name, type, values);
                }
                else
                {
                    column = new ProtoSpecColumn(name, type);
                }

                subset.Columns.Add(column);
            }

            if (CurrentToken.Type != ProtoSpecTokenType.RightBrace)
            {
                Error("规格定义结束位置，缺少'}'");
            }

            return subset;
        }

        private ProtoSpecEnumValueList ParseEnum()
        {
            NextToken();

            if (CurrentToken.Type != ProtoSpecTokenType.LeftBrace)
            {
                Error("枚举定义起始位置，缺少'{'");
            }

            ProtoSpecEnumValueList result = new ProtoSpecEnumValueList();

            NextToken();

            while (true)
            {
                if (CurrentToken.Type != ProtoSpecTokenType.Identifier)
                {
                    if (CurrentToken.Type != ProtoSpecTokenType.EndOfFile && CurrentToken.Type != ProtoSpecTokenType.RightBrace)
                    {
                        Error("枚举值缺少名称");
                    }

                    break;
                }

                string name = CurrentToken.Text;

                ProtoSpecEnumValue value = null;

                NextToken();

                if (CurrentToken.Type == ProtoSpecTokenType.Equal)
                {
                    NextToken();

                    if (CurrentToken.Type == ProtoSpecTokenType.Numeral)
                    {
                        value = SetEnumValue(name, int.Parse(CurrentToken.Text));

                        NextToken();
                    }
                    else
                    {
                        Error("枚举值必须是0 ~ 255之间的数值");
                    }
                }
                else
                {
                    value = AllocEnumValue(name);
                }

                result.Add(value);
            }

            if (CurrentToken.Type != ProtoSpecTokenType.RightBrace)
            {
                Error("枚举定义结束位置，缺少'}'");
            }

            return result;
        }

        private bool IsColumnType(ProtoSpecTokenType type)
        {
            return type >= ProtoSpecTokenType.Byte && type <= ProtoSpecTokenType.TypeOf;
        }

        private void Error(string message)
        {
            if (CurrentModule != null)
                throw new Exception("模块 " + CurrentModule + " 第" + (Scanner.CurrentLine + LineOffset) + "行 : " + message);
            else
                throw new Exception("第" + (Scanner.CurrentLine + LineOffset) + "行 : " + message);
        }
    }
}