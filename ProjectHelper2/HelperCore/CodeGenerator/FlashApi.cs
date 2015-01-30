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
        public static bool GenerateFlashCode(bool isConsole)
        {
            List<ProtoSpecModule> moduleList = GetModuleList(isConsole);

            if (moduleList == null)
                return false;

#if DEBUG
            string dir = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\client\\com\\protocols");
#else
			string dir = Path.Combine(Environment.CurrentDirectory, "client\\com\\protocols");
#endif
			dir = FixPath(dir);

            foreach (ProtoSpecModule module in moduleList)
            {
                if (module.ModuleId == AdminModuleId)
                    continue;

                StringBuilder code = new StringBuilder();

                string className = string.Format("Mod_{0}_Base", FormatName(module.Name));

                code.AppendFormat(
@"package com.protocols
{{
    import com.haloer.utils.*;
                            
	public class {0}
	{{", className);

                foreach (ProtoSpecEnumValue value in module.EnumValues)
                {
                    code.AppendFormat(
                        "\r\n        public static const {0}:int = {1};", value.Name.ToUpper(), value.Value
                    );
                }

                if (module.EnumValues.Count > 0)
                    code.AppendLine();

                foreach (ProtoSpecAction action in module.Actions)
                {
                    code.AppendFormat(
"\r\n        public static const {0} : Object = {{", action.Name
);
                    code.AppendLine();

                    code.Append(
"            'module'   : " + module.ModuleId + ","
);
                    code.AppendLine();

                    code.Append(
"            'action'   : " + action.ActionId + ","
);
                    code.AppendLine();

                    code.Append(
"            'request'  : ["
);

                    code.AppendLine();

                    for (int i = 0; i < action.Input.Columns.Count; i++)
                    {
                        code.Append(
                            GetColumnUtilCode(4, action.Input.Columns[i])
                        );

                        if (i < action.Input.Columns.Count - 1)
                            code.AppendLine(",");
                        else
                            code.AppendLine();
                    }

                    code.Append(
"            ],"
);
                    code.AppendLine();

                    code.Append(
"            'response' : ["
);

                    code.AppendLine();

                    for (int i = 0; i < action.Output.Columns.Count; i++)
                    {
                        code.Append(
                            GetColumnUtilCode(4, action.Output.Columns[i])
                        );

                        if (i < action.Output.Columns.Count - 1)
                            code.AppendLine(",");
                        else
                            code.AppendLine();
                    }

                    code.Append(
"            ]"
);
                    code.AppendLine();

                    code.Append(
"        };").AppendLine();
                }


                code.AppendLine(
@"
        public static const Actions : Array = [");

                int n = 0;

                foreach (ProtoSpecAction action in module.Actions)
                {
                    code.Append("            \"").Append(action.Name).Append("\"");

                    if (n < module.Actions.Count - 1)
                        code.AppendLine(",");
                    else
                        code.AppendLine();

                    n += 1;
                }

                code.Append(
@"        ];
    }
}");
                code.AppendLine();
                code.AppendLine();

                using (StreamWriter writer = new StreamWriter(Path.Combine(dir, className + ".as"), false))
                {
                    writer.Write(code.ToString());
                }
            }

            #region Mod.as
            {
                StringBuilder code = new StringBuilder();

                code.Append(
@"package com.protocols
{
    public class Mod
	{
	    public static const Modules : Object = {
");
                int n = 0;

                List<ProtoSpecModule> normalModules = new List<ProtoSpecModule>();

                foreach (ProtoSpecModule module in moduleList)
                {
                    if (module.ModuleId != AdminModuleId)
                    {
                        normalModules.Add(module);
                    }
                }

                foreach (ProtoSpecModule module in normalModules)
                {
                    code.AppendFormat("	        '{0}' : Mod_{1}_Base", module.ModuleId, FormatName(module.Name));

                    if (n < normalModules.Count - 1)
                        code.AppendLine(",");
                    else
                        code.AppendLine();

                    n += 1;
                }

                code.Append(
@"        };
	}
}");

                using (StreamWriter writer = new StreamWriter(Path.Combine(dir, "Mod.as"), false))
                {
                    writer.Write(code.ToString());
                }
            }
            #endregion

            Info("客户端代码生产完毕");

            return true;
        }

        private static string GetColumnUtilCode(int tab, ProtoSpecColumn column)
        {
            string tabcode = "";

            for (int i = 0; i < tab; i++)
                tabcode += "    ";

            string code = tabcode;

            switch (column.ColumnType)
            {
                case ProtoSpecColumnType.Byte:
                case ProtoSpecColumnType.Enum:
                    code += "Utils.ByteUtil";
                    break;

                case ProtoSpecColumnType.Short:
                    code += "Utils.ShortUtil";
                    break;

                case ProtoSpecColumnType.Int:
                    code += "Utils.IntUtil";
                    break;

                case ProtoSpecColumnType.Long:
                    code += "Utils.LongUtil";
                    break;

                case ProtoSpecColumnType.String:
                    code += "Utils.StringUtil";
                    break;

                case ProtoSpecColumnType.List:
                    code += "[\r\n";

                    for (int i = 0; i < column.Format.Columns.Count; i++)
                    {
                        code += GetColumnUtilCode(tab + 1, column.Format.Columns[i]);

                        if (i < column.Format.Columns.Count - 1)
                            code += ",\r\n";
                        else
                            code += "\r\n";
                    }

                    code += tabcode + "]\r\n";
                    break;

                case ProtoSpecColumnType.TypeOf:
                    for (int i = 0; i < column.Format.Columns.Count; i++)
                    {
                        code += GetColumnUtilCode(tab, column.Format.Columns[i]);

                        if (i < column.Format.Columns.Count - 1)
                            code += ",\r\n";
                        else
                            code += "\r\n";
                    }
                    break;
            }

            return code;
        }

	}
}

