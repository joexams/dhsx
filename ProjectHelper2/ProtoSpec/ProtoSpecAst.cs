using System;
using System.Collections.ObjectModel;

namespace ProtoSpec
{
    public class ProtoSpecDocument
    {
        public ProtoSpecDocument()
        {
            Modules = new ProtoSpecModuleList(this);
        }

        public ProtoSpecModuleList Modules
        {
            get;
            private set;
        }

        public ProtoSpecClassDef GetClassDef(string classModule, string className, bool ignoreCase)
        {
            ProtoSpecModule module = Modules.GetByName(classModule, ignoreCase);

            if (module == null)
                return null;

            ProtoSpecClassDef classDef = module.Classes.GetByName(className, ignoreCase);

            return classDef;
        }
    }

    public class ProtoSpecModule
    {
        public ProtoSpecModule(string name, string moduleId)
        {
            Name = name;
            ModuleId = moduleId;
            Actions = new ProtoSpecActionList(this);
            EnumValues = new ProtoSpecEnumValueList();
            Classes = new ProtoSpecClassDefList(this);
        }

        public string Name
        {
            get;
            private set;
        }

        public string ModuleId
        {
            get;
            private set;
        }

        public ProtoSpecDocument ParentDocument
        {
            get;
            set;
        }

        public ProtoSpecActionList Actions
        {
            get;
            private set;
        }

        public ProtoSpecEnumValueList EnumValues
        {
            get;
            private set;
        }

        public ProtoSpecClassDefList Classes
        {
            get;
            private set;
        }
    }

    public class ProtoSpecModuleList : Collection<ProtoSpecModule>
    {
        public ProtoSpecModuleList(ProtoSpecDocument document)
        {
            Document = document;
        }

        public ProtoSpecDocument Document
        {
            get;
            private set;
        }

        protected override void InsertItem(int index, ProtoSpecModule item)
        {
            item.ParentDocument = Document;

            base.InsertItem(index, item);
        }

        public ProtoSpecModule GetByName(string name, bool ignoreCase)
        {
            foreach (ProtoSpecModule module in this)
            {
                if (string.Compare(module.Name, name, ignoreCase) == 0)
                    return module;
            }

            return null;
        }

        public ProtoSpecModule GetById(string id)
        {
            foreach (ProtoSpecModule module in this)
            {
                if (module.ModuleId == id)
                    return module;
            }

            return null;
        }
    }

    public class ProtoSpecAction
    {
        public ProtoSpecAction(string name, string actionId)
        {
            Name = name;
            ActionId = actionId;
        }

        public string Name
        {
            get;
            private set;
        }

        public string ActionId
        {
            get;
            private set;
        }

        public ProtoSpecSubset Input
        {
            get;
            set;
        }

        public ProtoSpecSubset Output
        {
            get;
            set;
        }

        public ProtoSpecModule ParentModule
        {
            get;
            set;
        }
    }

    public class ProtoSpecActionList : Collection<ProtoSpecAction>
    {
        public ProtoSpecActionList(ProtoSpecModule parentModule)
        {
            ParentModule = parentModule;
        }

        public ProtoSpecModule ParentModule
        {
            get;
            private set;
        }

        protected override void InsertItem(int index, ProtoSpecAction item)
        {
            item.ParentModule = ParentModule;

            base.InsertItem(index, item);
        }

        public ProtoSpecAction GetByName(string name, bool ignoreCase)
        {
            foreach (ProtoSpecAction action in this)
            {
                if (string.Compare(action.Name, name, ignoreCase) == 0)
                    return action;
            }

            return null;
        }

        public ProtoSpecAction GetById(string id)
        {
            foreach (ProtoSpecAction action in this)
            {
                if (action.ActionId == id)
                    return action;
            }

            return null;
        }
    }

    public class ProtoSpecSubset
    {
        public ProtoSpecSubset(ProtoSpecAction parentAction)
        {
            Columns = new ProtoSpecColumnList(parentAction);

            ParentAction = parentAction;
        }

        public ProtoSpecColumnList Columns
        {
            get;
            private set;
        }

        public ProtoSpecAction ParentAction
        {
            get;
            set;
        }
    }

    public class ProtoSpecColumn
    {
        public ProtoSpecColumn(string name, ProtoSpecColumnType columnType)
        {
            Name = name;
            ColumnType = columnType;
        }

        public ProtoSpecColumn(string name, ProtoSpecColumnType columnType, ProtoSpecSubset format)
        {
            Name = name;
            ColumnType = columnType;
            Format = format;
        }

        public ProtoSpecColumn(string name, ProtoSpecColumnType columnType, ProtoSpecEnumValueList values)
        {
            Name = name;
            ColumnType = columnType;
            Values = values;
        }

        public ProtoSpecColumn(string name, ProtoSpecColumnType columnType, string className)
        {
            Name = name;
            ColumnType = columnType;
            ClassName = className;
        }

        public ProtoSpecColumn(string name, ProtoSpecColumnType columnType, string classModule, string className)
        {
            Name = name;
            ColumnType = columnType;
            ClassModule = classModule;
            ClassName = className;
        }

        public string Name
        {
            get;
            private set;
        }

        public ProtoSpecColumnType ColumnType
        {
            get;
            private set;
        }

        private ProtoSpecSubset m_Format;

        public ProtoSpecSubset Format
        {
            get
            {
                if (ClassName != null)
                {
                    ProtoSpecClassDef classDef = GetClassDef(ParentAction, ClassModule, ClassName);

                    m_Format = classDef.GetFullSpec(this.ParentAction);
                }

                return m_Format;
            }

            set
            {
                m_Format = value;
            }
        }

        public ProtoSpecEnumValueList Values
        {
            get;
            private set;
        }

        public string ClassModule
        {
            get;
            private set;
        }

        public string ClassName
        {
            get;
            private set;
        }

        public ProtoSpecAction ParentAction
        {
            get;
            set;
        }

        public ProtoSpecColumn Colon(ProtoSpecAction parentAction)
        {
            ProtoSpecColumn result = new ProtoSpecColumn(Name, this.ColumnType);

            result.Values = this.Values;
            result.ClassModule = this.ClassModule;
            result.ClassName = this.ClassName;
            result.ParentAction = parentAction;

            ProtoSpecSubset format = null;

            if (ClassName != null)
            {
                ProtoSpecClassDef classDef = GetClassDef(parentAction, ClassModule, ClassName);

                format = classDef.GetFullSpec(parentAction);
            }
            else
            {
                format = this.Format;
            }

            if (format != null)
            {
                ProtoSpecSubset newformat = new ProtoSpecSubset(parentAction);

                foreach (ProtoSpecColumn column in format.Columns)
                {
                    newformat.Columns.Add(column.Colon(parentAction));
                }

                result.Format = newformat;
            }

            return result;
        }

        private static ProtoSpecClassDef GetClassDef(ProtoSpecAction parentAction, string classModule, string className)
        {
            ProtoSpecModule module = parentAction.ParentModule;

            if (classModule != null)
            {
                module = parentAction.ParentModule.ParentDocument.Modules.GetByName(classModule, true);

                if (module == null)
                    throw new Exception("模块'" + parentAction.ParentModule.Name + "'的操作'" + parentAction.Name + "'中引用了不存在的模块'" + classModule + "'");
            }

            ProtoSpecClassDef classDef = null;

            if (classModule == null)
                classDef = parentAction.ParentModule.ParentDocument.GetClassDef(parentAction.ParentModule.Name, className, true);
            else
                classDef = parentAction.ParentModule.ParentDocument.GetClassDef(classModule, className, true);

            if (classDef == null)
            {
                if (classModule != null)
                    throw new Exception("模块'" + parentAction.ParentModule.Name + "'的操作'" + parentAction.Name + "'中引用了不存在的类'" + classModule + "." + className + "'");
                else
                    throw new Exception("模块'" + parentAction.ParentModule.Name + "'的操作'" + parentAction.Name + "'中引用了不存在的类'" + className + "'");
            }

            return classDef;
        }
    }

    public class ProtoSpecColumnList : Collection<ProtoSpecColumn>
    {
        public ProtoSpecColumnList(ProtoSpecAction parentAction)
        {
            ParentAction = parentAction;
        }

        public ProtoSpecAction ParentAction
        {
            get;
            private set;
        }

        protected override void InsertItem(int index, ProtoSpecColumn item)
        {
            item.ParentAction = ParentAction;

            base.InsertItem(index, item);
        }
    }

    public enum ProtoSpecColumnType
    {
        Byte    = 20,
        Short   = 21,
        Int     = 22,
        Long    = 23,
        String  = 24,
        List    = 25,
        Enum    = 26,
        TypeOf  = 27
    }

    public class ProtoSpecEnumValue
    {
        public ProtoSpecEnumValue(string name, int value)
        {
            Name = name;
            Value = value;
        }

        public string Name { get; private set; }

        public int Value { get; private set; }
    }

    public class ProtoSpecEnumValueList : Collection<ProtoSpecEnumValue>
    {
        public string GetNameByValue(int value)
        {
            foreach (ProtoSpecEnumValue item in this)
            {
                if (item.Value == value)
                    return item.Name;
            }

            return null;
        }

        public int GetValueByName(string name, bool ignoreCase)
        {
            foreach (ProtoSpecEnumValue item in this)
            {
                if (string.Compare(item.Name, name, ignoreCase) == 0)
                    return item.Value;
            }

            return -1;
        }
    }

    public class ProtoSpecClassDef
    {
        public ProtoSpecClassDef(string className, string classBaseModule, string classBaseName, ProtoSpecSubset classBody)
        {
            ClassName = className;
            ClassBaseModule = classBaseModule;
            ClassBaseName = classBaseName;
            ClassBody = classBody;
        }

        public string ClassName
        {
            get;
            private set;
        }

        public string ClassBaseModule
        {
            get;
            private set;
        }

        public string ClassBaseName
        {
            get;
            private set;
        }

        public ProtoSpecSubset ClassBody
        {
            get;
            private set;
        }

        public ProtoSpecModule ParentModule
        {
            get;
            set;
        }

        public ProtoSpecSubset GetFullSpec(ProtoSpecAction action)
        {
            ProtoSpecSubset resultSpec = new ProtoSpecSubset(action);

            if (ClassBaseName != null)
            {
                ProtoSpecClassDef classBaseDef = null;

                if (ClassBaseModule != null)
                    classBaseDef = ParentModule.ParentDocument.GetClassDef(ClassBaseModule, ClassBaseName, true);
                else
                    classBaseDef = ParentModule.ParentDocument.GetClassDef(ParentModule.Name, ClassBaseName, true);

                if (classBaseDef == null)
                {
                    if (ClassBaseModule != null)
                        throw new Exception("无法找到类型'" + ParentModule.Name + "." + ClassName + "'的基类'" + ClassBaseModule + "." + ClassBaseName + "'");
                    else
                        throw new Exception("无法找到类型'" + ParentModule.Name + "." + ClassName + "'的基类'" + ClassBaseName + "'");
                }

                ProtoSpecSubset classBaseSpec = classBaseDef.GetFullSpec(action);


                foreach (ProtoSpecColumn column in classBaseSpec.Columns)
                {
                    resultSpec.Columns.Add(column.Colon(action));
                }
            }

            foreach (ProtoSpecColumn column in ClassBody.Columns)
            {
                resultSpec.Columns.Add(column.Colon(action));
            }

            return resultSpec;
        }
    }

    public class ProtoSpecClassDefList : Collection<ProtoSpecClassDef>
    {
        public ProtoSpecClassDefList(ProtoSpecModule parentModule)
        {
            ParentModule = parentModule;
        }

        public ProtoSpecModule ParentModule
        {
            get;
            private set;
        }

        protected override void InsertItem(int index, ProtoSpecClassDef item)
        {
            item.ParentModule = ParentModule;

            base.InsertItem(index, item);
        }

        public ProtoSpecClassDef GetByName(string name, bool ignoreCase)
        {
            foreach (ProtoSpecClassDef classDef in this)
            {
                if (string.Compare(name, classDef.ClassName, ignoreCase) == 0)
                    return classDef;
            }

            return null;
        }
    }
}
