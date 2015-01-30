using System;
using System.IO;
using System.Text;
using System.Collections;
using MySql.Data.MySqlClient;
using System.Collections.Generic;
using System.Text.RegularExpressions;

using ProtoSpec;

namespace HelperCore
{
    public static partial class CodeGenerator
	{
        public static bool CanCompress(string moduleId, string actionId)
        {
            return
                (moduleId == "4"  && actionId == "2")  || 
                (moduleId == "14" && actionId == "11") || 
                (moduleId == "19" && actionId == "0")  ||
                (moduleId == "23" && actionId == "5")  ||
                (moduleId == "24" && actionId == "2")  ||
                (moduleId == "28" && actionId == "2")  ||
                (moduleId == "29" && actionId == "10") ||
                (moduleId == "30" && actionId == "11") ||
                (moduleId == "30" && actionId == "0");
        }

        const string AdminModuleId = "99";

        public static bool GenerateErlangCode2(bool isConsole, bool developMode)
        {
            List<ProtoSpecModule> moduleList = GetModuleList(isConsole);

            if (moduleList == null)
                return false;

#if DEBUG
            string includeDir2 = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new\\include"));
            string serverDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new"));
#else
            string includeDir2 = Path.Combine(Environment.CurrentDirectory, "server-new\\include");
            string serverDir = Path.Combine(Environment.CurrentDirectory, "server-new");
#endif
			includeDir2 = FixPath(includeDir2);
			serverDir = FixPath(serverDir);

            StringBuilder router = new StringBuilder();

            router.Append("-module(game_router).").AppendLine();
            router.Append("-export([route_request/2]).").AppendLine();
            router.Append("-include(\"game.hrl\").").AppendLine().AppendLine();

            router.Append("route_request(<<Module:8/unsigned, Action:8/unsigned, Args/binary>>, State) -> ").AppendLine();

            if (developMode)
            {
                //router.Append("    io:format(\"Call ~p --> ~p~n\", [Module, Action]),").AppendLine();
                router.Append("    {Time1, _} = statistics(runtime),").AppendLine();
                router.Append("    {Time2, _} = statistics(wall_clock),").AppendLine();
                router.Append("    {M, A, NewState} =");
            }

            router.Append("    route_request(Module, Action, Args, State)");

            if (developMode)
            {
                router.Append(",").AppendLine();
                router.Append("    {Time3, _} = statistics(runtime),").AppendLine();
                router.Append("    {Time4, _} = statistics(wall_clock),").AppendLine();
                router.Append("    Sec1 = (Time3 - Time1) / 1000.0,").AppendLine();
                router.Append("    Sec2 = (Time4 - Time2) / 1000.0,").AppendLine();
                router.Append("    game_prof_srv:set_info(M, A, Sec1, Sec2),").AppendLine();
                router.Append("    NewState.").AppendLine();
            }
            else
            {
                router.Append(".").AppendLine();
            }

            int n = 0;

            int headIndex = 0;

            int listParserCount = 0;
            int typeParserCount = 0;

            List<string> listParsers = new List<string>();
            List<string> typeParsers = new List<string>();

            foreach (ProtoSpecModule module in moduleList)
            {
                router.Append("route_request(").Append(module.ModuleId).Append(", _Action, _Args0, _State) -> ").AppendLine();

                if (!developMode && Convert.ToInt16(module.ModuleId) == 99)
                {
                    //允许访问后台接口IP
                    string admin_ips = "[\n" +
                        "        {10, 182, 1, 71}, {10, 182, 1, 72}, {10,190,233,245}, {10,190,233,235}, %srv \n" +
                        "        {10, 182, 0, 38}, {10, 182, 0, 39}, {10,207,251,82}  %web \n" +
                    "    ]";

                    router.Append("    {ok, {Address, _Port}} = inet:peername(_State #client_state.sock),").AppendLine();
                    router.Append("    case lists:member(Address, ").Append(admin_ips).Append(") of").AppendLine();
                    router.Append("        true -> ok;\n        _ -> exit({invalid_ip, Address})\n    end,").AppendLine().AppendLine();
                }

                router.Append("    case _Action of").AppendLine();

                for (int i = 0; i < module.Actions.Count; i++)
                {
                    ProtoSpecAction action = module.Actions[i];

                    router.Append("        ").Append(action.ActionId).Append(" -> ").AppendLine();

                    if (action.Input.Columns.Count > 0)
                    {
                        int n2 = 0;
                        int argsCount = 0;
                        int lenCount = 1;
                        int sizeCount = 1;

                    ParseInput:

                        router.Append("            <<");

                        bool inList = false;
                        bool inType = false;
                        ProtoSpecColumn listColumn = null;
                        ProtoSpecColumn typeColumn = null;

                        for (int j = n2; j < action.Input.Columns.Count; j ++)
                        {
                            ProtoSpecColumn column = action.Input.Columns[j];

                            switch (column.ColumnType)
                            {
                                case ProtoSpecColumnType.Byte:
                                    router.Append(FormatName(column.Name)).Append(":8/signed");
                                    break;
                                case ProtoSpecColumnType.Enum:
                                    router.Append(FormatName(column.Name)).Append(":8/unsigned");
                                    break;
                                case ProtoSpecColumnType.Short:
                                    router.Append(FormatName(column.Name)).Append(":16/signed");
                                    break;
                                case ProtoSpecColumnType.Int:
                                    router.Append(FormatName(column.Name)).Append(":32/signed");
                                    break;
                                case ProtoSpecColumnType.Long:
                                    router.Append(FormatName(column.Name)).Append(":64/signed");
                                    break;
                                case ProtoSpecColumnType.String:
                                    router.Append("Len").Append(lenCount).Append(":16/unsigned, ").Append(FormatName(column.Name)).Append(":Len").Append(lenCount).Append("/binary");
                                    lenCount += 1;
                                    break;
                                case ProtoSpecColumnType.List:
                                    router.Append("Size").Append(sizeCount).Append(":16/unsigned, ").Append(FormatName(column.Name)).Append("Bin/binary");
                                    sizeCount += 1;
                                    inList = true;
                                    listColumn = column;
                                    break;
                                case ProtoSpecColumnType.TypeOf:
                                    router.Append(FormatName(column.Name)).Append("Bin/binary");
                                    sizeCount += 1;
                                    inType = true;
                                    typeColumn = column;
                                    break;
                            }

                            n2 += 1;

                            if (inList || inType)
                                break;

                            if (n2 < action.Input.Columns.Count)
                                router.Append(", ");
                        }

                        router.Append(">> = _Args").Append(argsCount).Append(",").AppendLine();

                        if (inList)
                        {
                            argsCount += 1;

                            int newParserId = GenerateListParser(ref listParserCount, ref typeParserCount, listParsers, typeParsers, listColumn.Format);

                            router.Append("            {").Append(FormatName(listColumn.Name)).Append(", _Args").Append(argsCount).Append("}").Append(" = list_parser_").Append(newParserId).Append("(").Append("Size").Append(sizeCount - 1).Append(", ").Append(FormatName(listColumn.Name)).Append("Bin, []),").AppendLine();

                            if (n2 < action.Input.Columns.Count)
                                goto ParseInput;
                        }
                        else if (inType)
                        {
                            argsCount += 1;

                            int newParserId = GenerateTypeParser(ref listParserCount, ref typeParserCount, listParsers, typeParsers, typeColumn.Format);

                            router.Append("            {").Append(FormatName(typeColumn.Name)).Append(", _Args").Append(argsCount).Append("}").Append(" = type_parser_").Append(newParserId).Append("(").Append(FormatName(typeColumn.Name)).Append("Bin),").AppendLine();

                            if (n2 < action.Input.Columns.Count)
                                goto ParseInput;
                        }
                    }

                    if (developMode)
                    {
                        router.Append("            NewState ="); 
                    }

                    router.Append("            api_").Append(module.Name).Append(":").Append(action.Name).Append("(");

                    foreach (ProtoSpecColumn column in action.Input.Columns)
                    {
                        if (column.ColumnType == ProtoSpecColumnType.String)
                            router.Append("binary_to_list(").Append(FormatName(column.Name)).Append(")");
                        else
                            router.Append(FormatName(column.Name));

                        router.Append(", ");
                    }

                    router.Append("_State)");

                    if (developMode)
                    {
                        router.Append(",").AppendLine();
                        //router.Append("            io:format(\"~p : ~p~n\", [").Append(module.Name).Append(", ").Append(action.Name).Append("]),").AppendLine();
                        router.Append("            {").Append(module.Name).Append(", ").Append(action.Name).Append(", NewState}");
                    }

                    if (i < module.Actions.Count - 1)
                        router.AppendLine(";");
                    else
                        router.AppendLine();
                }
                router.Append("    end");

                if (n < moduleList.Count - 1)
                    router.AppendLine(";");
                else
                    router.AppendLine(".");

                router.AppendLine();


                StringBuilder head = new StringBuilder();

                foreach (ProtoSpecEnumValue value in module.EnumValues)
                {
                    head.AppendFormat(
                        "-define({0}, {1}).", value.Name.ToUpper(), value.Value
                    ).AppendLine();
                }

                StringBuilder code = new StringBuilder();

                code.AppendLine();

                int autoFunCount = 0;

                List<string> autoFunList = new List<string>();

                int level = 1;

                code.Append("-module(api_" + module.Name + "_out).").AppendLine();

                List<ProtoSpecAction> outputActions = new List<ProtoSpecAction>();

                for (int i = 0; i < module.Actions.Count; i++)
                {
                    if (module.Actions[i].Output.Columns.Count == 0)
                        continue;

                    outputActions.Add(module.Actions[i]);
                }

                code.Append("-export([").AppendLine();

                for (int i = 0; i < outputActions.Count; i++)
                {
                    code.Append("    ").Append(outputActions[i].Name).Append("/1");

                    if (i < outputActions.Count - 1)
                        code.AppendLine(",");
                    else
                        code.AppendLine();
                }

                code.Append("]).").AppendLine();
                code.AppendLine();

                for (int i = 0; i < outputActions.Count; i++)
                {
                    code.Append(
                        GenerateErlangFunction2(
                            module.ModuleId,
                            outputActions[i].ActionId,
                            outputActions[i].Name,
                            outputActions[i].Output,
                            ref autoFunCount,
                            ref autoFunList,
                            ref level,
                            false,
                            developMode
                        )
                    );

                    code.AppendLine();
                }

                code.AppendLine();
                code.AppendLine();

                for (int i = 0; i < autoFunList.Count; i++)
                {
                    code.Append(autoFunList[i]);
                }

                using (StreamWriter writer = new StreamWriter(FixPath(Path.Combine(includeDir2, "gen\\api_" + module.Name + ".hrl")), false))
                {
                    writer.Write(head.ToString());

                    writer.WriteLine();
                }

                using (StreamWriter writer = new StreamWriter(FixPath(Path.Combine(serverDir, "src\\gen\\api_" + module.Name + "_out.erl")), false))
                {
                    writer.Write(code.ToString().Substring(headIndex));
                }

                n += 1;
            }

            for(int i = listParsers.Count - 1; i >= 0; i --)
            {
                router.AppendLine(listParsers[i]);
            }

            for (int i = typeParsers.Count - 1; i >= 0; i--)
            {
                router.AppendLine(typeParsers[i]);
            }

            using (StreamWriter writer = new StreamWriter(FixPath(Path.Combine(serverDir, "src\\gen\\game_router.erl")), false))
            {
                writer.Write(router.ToString());
            }

            Info("服务端代码生成完毕");

            return true;
        }

        private static int GenerateListParser(ref int listParserCount, ref int typeParserCount, List<string> listParsers, List<string> typeParsers, ProtoSpecSubset format)
        {
            int id = listParserCount ++;

            StringBuilder router = new StringBuilder();

            router.Append("list_parser_").Append(id).Append("(0, _Args").Append(", _Result) ->").AppendLine();
            router.Append("    {_Result, _Args};").AppendLine();
            router.Append("list_parser_").Append(id).Append("(_Count, _Args0").Append(", _Result) ->").AppendLine();

            int n2 = 0;
            int argsCount = 0;
            int lenCount = 1;
            int sizeCount = 1;

        ParseInput:

            router.Append("    <<");

            bool inList = false;
            bool inType = false;

            ProtoSpecColumn listColumn = null;
            ProtoSpecColumn typeColumn = null;

            for (int j = n2; j < format.Columns.Count; j++)
            {
                ProtoSpecColumn column = format.Columns[j];

                switch (column.ColumnType)
                {
                    case ProtoSpecColumnType.Byte:
                        router.Append(FormatName(column.Name)).Append(":8/signed");
                        break;
                    case ProtoSpecColumnType.Enum:
                        router.Append(FormatName(column.Name)).Append(":8/unsigned");
                        break;
                    case ProtoSpecColumnType.Short:
                        router.Append(FormatName(column.Name)).Append(":16/signed");
                        break;
                    case ProtoSpecColumnType.Int:
                        router.Append(FormatName(column.Name)).Append(":32/signed");
                        break;
                    case ProtoSpecColumnType.Long:
                        router.Append(FormatName(column.Name)).Append(":64/signed");
                        break;
                    case ProtoSpecColumnType.String:
                        router.Append("Len").Append(lenCount).Append(":16/unsigned, ").Append(FormatName(column.Name)).Append(":Len").Append(lenCount).Append("/binary");
                        lenCount += 1;
                        break;
                    case ProtoSpecColumnType.List:
                        router.Append("Size").Append(sizeCount).Append(":16/unsigned, ").Append(FormatName(column.Name)).Append("Bin/binary");
                        sizeCount += 1;
                        inList = true;
                        listColumn = column;
                        break;
                    case ProtoSpecColumnType.TypeOf:
                        router.Append(FormatName(column.Name)).Append("Bin/binary");
                        sizeCount += 1;
                        inType = true;
                        typeColumn = column;
                        break;
                }

                n2 += 1;

                if (inList)
                    break;

                router.Append(", ");
            }

            argsCount += 1;

            if (inList)
            {
                router.Append(">> = _Args").Append(argsCount - 1).Append(",").AppendLine();

                int newParserId = GenerateListParser(ref listParserCount, ref typeParserCount, listParsers, typeParsers, listColumn.Format);

                router.Append("    {").Append(FormatName(listColumn.Name)).Append(", _Args").Append(argsCount).Append("}").Append(" = list_parser_").Append(newParserId).Append("(").Append("Size").Append(sizeCount - 1).Append(", ").Append(FormatName(listColumn.Name)).Append("Bin, []),").AppendLine();

                if (n2 < format.Columns.Count)
                    goto ParseInput;
            }
            else if (inType)
            {
                argsCount += 1;

                int newParserId = GenerateTypeParser(ref listParserCount, ref typeParserCount, listParsers, typeParsers, typeColumn.Format);

                router.Append("            {").Append(FormatName(typeColumn.Name)).Append(", _Args").Append(argsCount).Append("}").Append(" = type_parser_").Append(newParserId).Append("(").Append(FormatName(typeColumn.Name)).Append("Bin),").AppendLine();

                if (n2 < format.Columns.Count)
                    goto ParseInput;
            }
            else
            {
                router.Append("_Args").Append(argsCount).Append("/binary>> = _Args").Append(argsCount - 1).Append(",").AppendLine();
            }

            router.Append("    _NewItem = {");

            n2 = 0;

            foreach (ProtoSpecColumn column in format.Columns)
            {
                if (column.ColumnType == ProtoSpecColumnType.String)
                    router.Append("binary_to_list(").Append(FormatName(column.Name)).Append(")");
                else
                    router.Append(FormatName(column.Name));

                if (n2 < format.Columns.Count - 1)
                    router.Append(", ");

                n2 += 1;
            }

            router.Append("},").AppendLine();
            router.Append("    list_parser_").Append(id).Append("(_Count - 1, _Args").Append(argsCount).Append(", [_NewItem | _Result]).").AppendLine();

            listParsers.Add(router.ToString());

            return id;
        }

        private static int GenerateTypeParser(ref int listParserCount, ref int typeParserCount, List<string> listParsers, List<string> typeParsers, ProtoSpecSubset format)
        {
            int id = typeParserCount++;

            StringBuilder router = new StringBuilder();

            router.Append("type_parser_").Append(id).Append("(_Args0) ->").AppendLine();

            int n2 = 0;
            int argsCount = 0;
            int lenCount = 1;
            int sizeCount = 1;

        ParseInput:

            router.Append("    <<");

            bool inList = false;
            bool inType = false;

            ProtoSpecColumn listColumn = null;
            ProtoSpecColumn typeColumn = null;

            for (int j = n2; j < format.Columns.Count; j++)
            {
                ProtoSpecColumn column = format.Columns[j];

                switch (column.ColumnType)
                {
                    case ProtoSpecColumnType.Byte:
                        router.Append(FormatName(column.Name)).Append(":8/signed");
                        break;
                    case ProtoSpecColumnType.Enum:
                        router.Append(FormatName(column.Name)).Append(":8/unsigned");
                        break;
                    case ProtoSpecColumnType.Short:
                        router.Append(FormatName(column.Name)).Append(":16/signed");
                        break;
                    case ProtoSpecColumnType.Int:
                        router.Append(FormatName(column.Name)).Append(":32/signed");
                        break;
                    case ProtoSpecColumnType.Long:
                        router.Append(FormatName(column.Name)).Append(":64/signed");
                        break;
                    case ProtoSpecColumnType.String:
                        router.Append("Len").Append(lenCount).Append(":16/unsigned, ").Append(FormatName(column.Name)).Append(":Len").Append(lenCount).Append("/binary");
                        lenCount += 1;
                        break;
                    case ProtoSpecColumnType.List:
                        router.Append("Size").Append(sizeCount).Append(":16/unsigned, ").Append(FormatName(column.Name)).Append("Bin/binary");
                        sizeCount += 1;
                        inList = true;
                        listColumn = column;
                        break;
                    case ProtoSpecColumnType.TypeOf:
                        router.Append(FormatName(column.Name)).Append("Bin/binary");
                        sizeCount += 1;
                        inType = true;
                        typeColumn = column;
                        break;
                }

                n2 += 1;

                if (inList)
                    break;

                router.Append(", ");
            }

            argsCount += 1;

            if (inList)
            {
                router.Append(">> = _Args").Append(argsCount - 1).Append(",").AppendLine();

                int newParserId = GenerateListParser(ref listParserCount, ref typeParserCount, listParsers, typeParsers, listColumn.Format);

                router.Append("    {").Append(FormatName(listColumn.Name)).Append(", _Args").Append(argsCount).Append("}").Append(" = list_parser_").Append(newParserId).Append("(").Append("Size").Append(sizeCount - 1).Append(", ").Append(FormatName(listColumn.Name)).Append("Bin, []),").AppendLine();

                if (n2 < format.Columns.Count)
                    goto ParseInput;
            }
            else if (inType)
            {
                argsCount += 1;

                int newParserId = GenerateTypeParser(ref listParserCount, ref typeParserCount, listParsers, typeParsers, typeColumn.Format);

                router.Append("            {").Append(FormatName(typeColumn.Name)).Append(", _Args").Append(argsCount).Append("}").Append(" = type_parser_").Append(newParserId).Append("(").Append(FormatName(typeColumn.Name)).Append("Bin),").AppendLine();

                if (n2 < format.Columns.Count)
                    goto ParseInput;
            }
            else
            {
                router.Append("_Args").Append(argsCount).Append("/binary>> = _Args").Append(argsCount - 1).Append(",").AppendLine();
            }

            router.Append("    _NewItem = {");

            n2 = 0;

            foreach (ProtoSpecColumn column in format.Columns)
            {
                if (column.ColumnType == ProtoSpecColumnType.String)
                    router.Append("binary_to_list(").Append(FormatName(column.Name)).Append(")");
                else
                    router.Append(FormatName(column.Name));

                if (n2 < format.Columns.Count - 1)
                    router.Append(", ");

                n2 += 1;
            }

            router.Append("},").AppendLine();
            router.Append("    {_NewItem, _Args").Append(argsCount).Append("}.").AppendLine();

            typeParsers.Add(router.ToString());

            return id;
        }

        private static string GenerateErlangFunction2(string moduleID, string actionID, string name, ProtoSpecSubset format, ref int autoFunCount, ref List<string> autoFunList, ref int level, bool islist, bool developMode)
        {
            string[] columnBinDecl = new string[format.Columns.Count];
            string[] columnBinCode = new string[format.Columns.Count];

            StringBuilder code = new StringBuilder();

            code.Append(name).AppendLine(" ({");

            for (int i = 0; i < format.Columns.Count; i++)
            {
                ProtoSpecColumn column = format.Columns[i];

                string paramName = FormatName(column.Name);

                code.Append("    ").Append(paramName);

                if (i < format.Columns.Count - 1)
                    code.AppendLine(",");
                else
                    code.AppendLine();

                GenerateErlangVarBin2(paramName, column, ref columnBinDecl[i], ref columnBinCode[i], ref autoFunCount, ref autoFunList, ref level, developMode);
            }

            code.AppendLine("}) ->");

            for (int i = 0; i < columnBinDecl.Length; i++)
            {
                if (columnBinDecl[i] == null)
                    continue;

                code.AppendLine(columnBinDecl[i]);

                code.AppendLine();
            }

            if (moduleID != null)
            {
                if (moduleID != null && moduleID != AdminModuleId && !developMode)
                    code.AppendLine("    OutBin = <<");
                else
                    code.AppendLine("    <<");
                
                code.Append("        ").Append(moduleID).AppendLine(":8/unsigned,");
                code.Append("        ").Append(actionID).AppendLine(":8/unsigned,");
            }
            else
            {
                code.AppendLine("    <<");
            }

            for (int i = 0; i < columnBinCode.Length; i++)
            {
                code.Append("        ").Append(columnBinCode[i]);

                if (i < columnBinCode.Length - 1)
                    code.AppendLine(",");
                else
                    code.AppendLine();
            }

            if (moduleID != null && moduleID != AdminModuleId && !developMode)
            {
                code.Append("    >>,").AppendLine();
                code.Append("    OutBinSize = size(OutBin),").AppendLine();
                code.Append("    if OutBinSize >= 64 -> zlib:compress(OutBin); true -> OutBin end");
            }
            else
            {
                code.Append("    >>");
            }

            code.AppendLine(".");

            return code.ToString();
        }

        private static void GenerateErlangVarBin2(string name, ProtoSpecColumn column, ref string decl, ref string code, ref int autoFunCount, ref List<string> autoFunList, ref int level, bool developMode)
        {
            string tab = "";

            for (int i = 0; i < level; i++)
            {
                tab += "    ";
            }

            switch (column.ColumnType)
            {
                case ProtoSpecColumnType.Byte:
                    code = name + ":8/signed";
                    break;
                case ProtoSpecColumnType.Enum:
                    code = name + ":8/unsigned";
                    break;
                case ProtoSpecColumnType.Short:
                    code = name + ":16/signed";
                    break;
                case ProtoSpecColumnType.Int:
                    code = name + ":32/signed";
                    break;
                case ProtoSpecColumnType.Long:
                    code = name + ":64/signed";
                    break;
                case ProtoSpecColumnType.String:
                    decl = string.Format("{1}Bin_{0} = list_to_binary({0}), \r\n{1}Bin_{0}_Len = size(Bin_{0}),", name, tab);

                    code = string.Format("Bin_{0}_Len:16/unsigned, Bin_{0}/binary", name);
                    break;
                case ProtoSpecColumnType.List:
                    {
                        string funName = "item_to_bin_" + autoFunCount.ToString();

                        string tempVar = name + "_Item";

                        decl = string.Format("{3}BinList_{0} = [\r\n{3}    {1}({2}) || {2} <- {0}\r\n{3}], \r\n\r\n{3}{0}_Len = length({0}), \r\n{3}Bin_{0} = list_to_binary(BinList_{0}),", name, funName, tempVar, tab);

                        code = string.Format("{0}_Len:16/unsigned, Bin_{0}/binary", name);

                        autoFunCount += 1;

                        autoFunList.Add(funName);

                        string funBody = GenerateErlangFunction2(null, null, funName, column.Format, ref autoFunCount, ref autoFunList, ref level, true, developMode);

                        autoFunList[autoFunList.IndexOf(funName)] = funBody;
                    }
                    break;
                case ProtoSpecColumnType.TypeOf:
                    {
                        string funName = "type_to_bin_" + autoFunCount.ToString();

                        string tempVar = name + "_Bin";

                        decl = tab + tempVar + " = type_to_bin_" + autoFunCount.ToString() + "(" + name + "),";

                        code = tempVar + "/binary";

                        autoFunCount += 1;

                        autoFunList.Add(funName);

                        string funBody = GenerateErlangFunction2(null, null, funName, column.Format, ref autoFunCount, ref autoFunList, ref level, true, developMode);

                        autoFunList[autoFunList.IndexOf(funName)] = funBody;
                    }
                    break;
            }
        }

	}
}

