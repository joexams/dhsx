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
    public static class CodeGenerator
    {
        public enum MessageType { Info, Error };

        public delegate void MessageHandler(MessageType type, string message);

        private static MessageHandler _errorHandler;

        public static void SetMessageHandler(MessageHandler handler)
        {
            _errorHandler = handler;
        }

        private static void Info(string message)
        {
            _errorHandler(MessageType.Info, message);
        }

        private static void Error(string message)
        {
            _errorHandler(MessageType.Error, message);
        }

        private static string GenerateSpace(int maxlength, int length)
        {
            int temp = maxlength - length;

            string space = " ";

            for (int j = 0; j < temp; j++)
            {
                space += " ";
            }

            return space;
        }

        static Regex formatNameRegex = new Regex("^(.)|_(.)");

        private static string FormatName(string name)
        {
            return formatNameRegex.Replace(name, delegate(Match match)
            {
                if (match.Groups[1].Success)
                    return char.ToUpper(match.Groups[1].Value[0]).ToString();
                else
                    return char.ToUpper(match.Groups[2].Value[0]).ToString();
            });
        }

        public static List<ProtoSpecModule> GetModuleList(bool isConsole)
        {
            try
            {
#if DEBUG
                string protoSpecDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new\\doc\\通讯协议"));
#else
				string protoSpecDir = Path.Combine(Environment.CurrentDirectory, "server-new\\doc\\通讯协议");
#endif

                List<ProtoSpecModule> moduleList = new List<ProtoSpecModule>();

                string protocol = null;

                string[] files = Directory.GetFiles(protoSpecDir);

                foreach (string file in files)
                {
                    if (Path.GetFileNameWithoutExtension(file) == "Readme")
                        continue;

                    protocol += File.ReadAllText(file, Encoding.UTF8);
                }

                ProtoSpecParser parser = new ProtoSpecParser(protocol, 0);

                ProtoSpecDocument document = parser.Parse();

                foreach (ProtoSpecModule module in document.Modules)
                {
                    moduleList.Add(module);
                }

                return moduleList;
            }
            catch (Exception ex)
            {
                Error(ex.Message);
            }

            return null;
        }

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

            foreach (ProtoSpecModule module in moduleList)
            {
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

                foreach (ProtoSpecModule module in moduleList)
                {
                    code.AppendFormat("	        '{0}' : Mod_{1}_Base", module.ModuleId, FormatName(module.Name));

                    if (n < moduleList.Count - 1)
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
            }

            return code;
        }

        public static bool GenerateFlashDatabaseCode2(bool isConsole, string server, string uid, string pwd, string database, string port)
        {
            try
            {
#if DEBUG
                string dir = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\client\\com\\assist\\server");
#else
				string dir = Path.Combine(Environment.CurrentDirectory, "client\\com\\assist\\server");
#endif

                if (Directory.Exists(dir) == false)
                    Directory.CreateDirectory(dir);

                string connectionString = "Server=" + server + ";Uid=" + uid + ";Pwd=" + pwd + ";Port=" + port + ";Database=" + database + ";";

                using (MySqlConnection connection = new MySqlConnection(connectionString))
                {
                    connection.Open();

                    #region NPC
                    {
                        List<Hashtable> npcList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `npc`";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable npc = new Hashtable();

                                    npc["id"] = reader.GetInt32("id");
                                    npc["name"] = reader.GetString("name");
                                    npc["sign"] = reader.GetString("sign");
                                    npc["dialog"] = reader.GetString("dialog");
                                    npc["shop_name"] = reader.GetString("shop_name");
                                    npc["npc_func_id"] = reader.GetInt32("npc_func_id");

                                    npcList.Add(npc);
                                }

                                reader.Close();
                            }
                        }

                        List<Hashtable> npcFuncList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `npc_function`";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable npcFunc = new Hashtable();

                                    npcFunc["id"] = reader.GetInt32("id");
                                    npcFunc["name"] = reader.GetString("name");
                                    npcFunc["sign"] = reader.GetString("sign");

                                    npcFuncList.Add(npcFunc);
                                }

                                reader.Close();
                            }
                        }


                        StringBuilder npcTypeBase = new StringBuilder();

                        npcTypeBase.Append(
    @"package com.assist.server
{
	public class NPCTypeBase
	{
");
                        for (int i = 0; i < npcFuncList.Count; i++)
                        {
                            Hashtable func = npcFuncList[i];

                            npcTypeBase.AppendFormat(
    "		public static const {0}:Number = {1}; //{2}",
    func["sign"], func["id"], func["name"]
    ).AppendLine();
                        }

                        npcTypeBase.Append(
    @"
        //格式 => id : [sign, name, dialog, shop_name, npc_func_id]
		public static const List : Object = {
");

                        for (int i = 0; i < npcList.Count; i++)
                        {
                            Hashtable npc = npcList[i];

                            npcTypeBase.AppendFormat(
                                "			{0} : [\"{1}\", \"{2}\", \"{3}\", \"{4}\", {5}]",
                                npc["id"], npc["sign"], npc["name"], npc["dialog"], npc["shop_name"], npc["npc_func_id"]
                            );

                            if (i < npcList.Count - 1)
                                npcTypeBase.AppendLine(",");
                        }


                        npcTypeBase.Append(
    @"
		};
	}
}");

                        using (StreamWriter writer = new StreamWriter(Path.Combine(dir, "NPCTypeBase.as"), false))
                        {
                            writer.Write(npcTypeBase.ToString());
                        }
                    }
                    #endregion

                    #region Town
                    {
                        List<Hashtable> townList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `town`";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable town = new Hashtable();

                                    town["id"] = reader.GetInt32("id");
                                    town["name"] = reader.GetString("name");
                                    town["sign"] = reader.GetString("sign");
                                    town["lock"] = reader.GetInt32("lock");
                                    town["description"] = reader.GetString("description");

                                    townList.Add(town);
                                }

                                reader.Close();
                            }
                        }

                        StringBuilder townTypeBase = new StringBuilder();

                        townTypeBase.Append(
    @"package com.assist.server
{
    public class TownTypeBase
    {
        //格式 => id : [{sign, name, npc_list => [sign : {id, x, y}]}, lock, description]
        public static const List : Object = {
");

                        for (int i = 0; i < townList.Count; i++)
                        {
                            Hashtable town = townList[i];

                            StringBuilder townNpcList = new StringBuilder();

                            using (MySqlCommand command = new MySqlCommand())
                            {
                                command.Connection = connection;
                                command.CommandText = "SELECT A.id, A.position_x, A.position_y, B.sign FROM `town_npc` AS A LEFT JOIN `npc` AS B ON B.id = A.npc_id WHERE `town_id` = " + town["id"];

                                using (MySqlDataReader reader = command.ExecuteReader())
                                {
                                    while (reader.Read())
                                    {
                                        townNpcList.Append("                \"").Append(reader.GetString("sign")).Append("\" : [").Append(reader.GetString("id")).Append(",").Append(reader.GetString("position_x")).Append(",").Append(reader.GetString("position_y")).AppendLine("],");
                                    }

                                    reader.Close();
                                }
                            }

                            townTypeBase.AppendFormat(
                                @"            {0} : [""{1}"", ""{2}"", {{
{3}
            }}, {4}, ""{5}""]",
                    town["id"], town["sign"], town["name"],
                    (townNpcList.Length > 0 ? townNpcList.ToString(0, townNpcList.Length - 3) : ""),
                    town["lock"], town["description"]
                            );

                            if (i < townList.Count - 1)
                                townTypeBase.AppendLine(",");
                        }

                        townTypeBase.Append(
    @"
        };
    }
}");

                        using (StreamWriter writer = new StreamWriter(Path.Combine(dir, "TownTypeBase.as"), false))
                        {
                            writer.Write(townTypeBase.ToString());
                        }
                    }
                    #endregion

                    #region Mission
                    {
                        List<Hashtable> sectionList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `mission_section` ORDER BY `lock` ASC";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable section = new Hashtable();

                                    section["id"] = reader.GetInt32("id");
                                    section["name"] = reader.GetString("name");
                                    section["sign"] = reader.GetString("sign");

                                    sectionList.Add(section);
                                }

                                reader.Close();
                            }
                        }

                        List<Hashtable> missionList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `mission` ORDER BY `lock` ASC";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable mission = new Hashtable();

                                    mission["id"] = reader.GetInt32("id");
                                    mission["name"] = reader.GetString("name");
                                    mission["mission_section_id"] = reader.GetInt32("mission_section_id");

                                    missionList.Add(mission);
                                }

                                reader.Close();
                            }
                        }

                        StringBuilder missionTypeBase = new StringBuilder();

                        missionTypeBase.Append(
    @"package com.assist.server
{
	public class MissionTypeBase
	{
        //格式 => id : [sign, name, index]
		public static const Section : Object = {
");

                        for (int i = 0; i < sectionList.Count; i++)
                        {
                            Hashtable section = sectionList[i];

                            missionTypeBase.AppendFormat(
                                "			{0} : [\"{1}\", \"{2}\", {3}]",
                                section["id"], section["sign"], section["name"], i
                            );

                            if (i < sectionList.Count - 1)
                                missionTypeBase.AppendLine(",");
                        }

                        missionTypeBase.Append(
    @"
		};
		
        //格式 => id : [mission_section_id, name, index]
		public static const Mission : Object = {
");

                        for (int i = 0; i < missionList.Count; i++)
                        {
                            Hashtable mission = missionList[i];

                            missionTypeBase.AppendFormat(
                                "			{0} : [{1}, \"{2}\", {3}]",
                                mission["id"], mission["mission_section_id"], mission["name"], i
                            );

                            if (i < missionList.Count - 1)
                                missionTypeBase.AppendLine(",");
                        }

                        missionTypeBase.Append(
    @"
		};
	}
}");
                        using (StreamWriter writer = new StreamWriter(Path.Combine(dir, "MissionTypeBase.as"), false))
                        {
                            writer.Write(missionTypeBase.ToString());
                        }
                    }
                    #endregion

                    #region Role/RoleJob

                    {
                        List<Hashtable> roleList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `role`";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable role = new Hashtable();

                                    role["id"] = reader.GetInt32("id");
                                    role["sign"] = reader.GetString("sign");
                                    role["name"] = reader.GetString("name");

                                    roleList.Add(role);
                                }

                                reader.Close();
                            }
                        }

                        List<Hashtable> roleJobList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `role_job`";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable roleJob = new Hashtable();

                                    roleJob["id"] = reader.GetInt32("id");
                                    roleJob["sign"] = reader.GetString("sign");
                                    roleJob["name"] = reader.GetString("name");

                                    roleJobList.Add(roleJob);
                                }

                                reader.Close();
                            }
                        }

                        StringBuilder roleTypeBase = new StringBuilder();

                        roleTypeBase.Append(
    @"package com.assist.server
{
	public class RoleTypeBase
	{
        //格式 => id : [sign, name]
		public static const Roles : Object = {
");

                        for (int i = 0; i < roleList.Count; i++)
                        {
                            Hashtable role = roleList[i];

                            roleTypeBase.AppendFormat(
                                "			{0} : [\"{1}\", \"{2}\"]",
                                role["id"], role["sign"], role["name"]
                            );

                            if (i < roleList.Count - 1)
                                roleTypeBase.AppendLine(",");
                        }

                        roleTypeBase.Append(
    @"
        };
        
        //格式 => id : [sign, name]
		public static const RoleJobs : Object = {
");
                        for (int i = 0; i < roleJobList.Count; i++)
                        {
                            Hashtable roleJob = roleJobList[i];

                            roleTypeBase.AppendFormat(
                                "			{0} : [\"{1}\", \"{2}\"]",
                                roleJob["id"], roleJob["sign"], roleJob["name"]
                            );

                            if (i < roleJobList.Count - 1)
                                roleTypeBase.AppendLine(",");
                        }

                        roleTypeBase.Append(
    @"
		};
	}
}");

                        using (StreamWriter writer = new StreamWriter(Path.Combine(dir, "RoleTypeBase.as"), false))
                        {
                            writer.Write(roleTypeBase.ToString());
                        }
                    }
                    #endregion

                    #region Item

                    {
                        List<Hashtable> itemTypeList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `item_type`";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable itemType = new Hashtable();

                                    itemType["id"] = reader.GetInt32("id");
                                    itemType["sign"] = reader.GetString("sign");
                                    itemType["name"] = reader.GetString("name");
                                    itemType["max_repeat_num"] = reader.GetString("max_repeat_num");

                                    itemTypeList.Add(itemType);
                                }

                                reader.Close();
                            }
                        }

                        List<Hashtable> avatarItemMonsterList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT A.item_id, B.sign FROM `avatar_item_monster` AS A LEFT JOIN `monster` AS B ON B.id = A.monster_id";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable avaterItemMonster = new Hashtable();

                                    avaterItemMonster["item_id"] = reader.GetInt32("item_id");
                                    avaterItemMonster["sign"] = reader.GetString("sign");

                                    avatarItemMonsterList.Add(avaterItemMonster);
                                }

                                reader.Close();
                            }
                        }

                        StringBuilder itemTypeBase = new StringBuilder();

                        itemTypeBase.Append(
    @"package com.assist.server
{
	public class ItemTypeBase
	{
        //格式 => id : [sign, name, max_repeat_num]
		public static const ItemTypes : Object = {
");

                        for (int i = 0; i < itemTypeList.Count; i++)
                        {
                            Hashtable itemType = itemTypeList[i];

                            itemTypeBase.AppendFormat(
                                "			{0} : [\"{1}\", \"{2}\", {3}]",
                                itemType["id"], itemType["sign"], itemType["name"], itemType["max_repeat_num"]
                            );

                            if (i < itemTypeList.Count - 1)
                                itemTypeBase.AppendLine(",");
                        }

                        itemTypeBase.Append(
    @"
        };
        
        //格式 => item_id : monster_sign
		public static const AvatarMonsters : Object = {
");
                        for (int i = 0; i < avatarItemMonsterList.Count; i++)
                        {
                            Hashtable avatarItemMonster = avatarItemMonsterList[i];

                            itemTypeBase.AppendFormat(
                                "			{0} : \"{1}\"",
                                avatarItemMonster["item_id"], avatarItemMonster["sign"]
                            );

                            if (i < avatarItemMonsterList.Count - 1)
                                itemTypeBase.AppendLine(",");
                        }

                        itemTypeBase.Append(
    @"
		};
	}
}");

                        using (StreamWriter writer = new StreamWriter(Path.Combine(dir, "ItemTypeBase.as"), false))
                        {
                            writer.Write(itemTypeBase.ToString());
                        }
                    }
                    #endregion

                    #region Monster

                    {
                        List<Hashtable> monsterList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT sign, talk FROM `monster`";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable monster = new Hashtable();

                                    monster["sign"] = reader.GetString("sign");
                                    monster["talk"] = reader.GetString("talk");

                                    monsterList.Add(monster);
                                }

                                reader.Close();
                            }
                        }

                        StringBuilder monsterTypeBase = new StringBuilder();

                        monsterTypeBase.Append(
    @"package com.assist.server
{
	public class MonsterTypeBase
	{
        //格式 => sign : [talk]
		public static const Monsters : Object = {
");

                        for (int i = 0; i < monsterList.Count; i++)
                        {
                            Hashtable itemType = monsterList[i];

                            monsterTypeBase.AppendFormat(
                                "			\"{0}\" : [\"{1}\"]",
                                itemType["sign"], itemType["talk"]
                            );

                            if (i < monsterList.Count - 1)
                                monsterTypeBase.AppendLine(",");
                        }

                        monsterTypeBase.Append(
    @"
        };
	}
}");

                        using (StreamWriter writer = new StreamWriter(Path.Combine(dir, "MonsterTypeBase.as"), false))
                        {
                            writer.Write(monsterTypeBase.ToString());
                        }
                    }
                    #endregion

                    #region Faction
                    {
                        List<Hashtable> factionClassList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `camp`";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable factionClass = new Hashtable();

                                    factionClass["id"] = reader.GetInt32("id");
                                    factionClass["sign"] = reader.GetString("sign");
                                    factionClass["name"] = reader.GetString("name");

                                    factionClassList.Add(factionClass);
                                }

                                reader.Close();
                            }
                        }

                        List<Hashtable> factionJobList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `faction_job`";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable factionJob = new Hashtable();

                                    factionJob["id"] = reader.GetInt32("id");
                                    factionJob["sign"] = reader.GetString("sign");
                                    factionJob["name"] = reader.GetString("name");

                                    factionJobList.Add(factionJob);
                                }

                                reader.Close();
                            }
                        }

                        List<Hashtable> factionLevelList = new List<Hashtable>();

                        using (MySqlCommand command = new MySqlCommand())
                        {
                            command.Connection = connection;
                            command.CommandText = "SELECT * FROM `faction_level`";

                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    Hashtable factionLevel = new Hashtable();

                                    factionLevel["id"] = reader.GetInt32("id");
                                    factionLevel["sign"] = reader.GetString("sign");
                                    factionLevel["faction_level_name"] = reader.GetString("faction_level_name");

                                    factionLevelList.Add(factionLevel);
                                }

                                reader.Close();
                            }
                        }

                        StringBuilder factionTypeBase = new StringBuilder();

                        factionTypeBase.Append(
    @"package com.assist.server
{
	public class FactionTypeBase
	{
");

                        for (int i = 0; i < factionClassList.Count; i++)
                        {
                            Hashtable factionClass = factionClassList[i];
                            factionTypeBase.AppendFormat(@"public static const {0} : String = ""{0}"";", factionClass["sign"]).AppendLine();
                        }

                        factionTypeBase.Append(
@"
        //格式 => id : [sign, name]
		public static const Camps : Object = {
");
                        for (int i = 0; i < factionClassList.Count; i++)
                        {
                            Hashtable factionClass = factionClassList[i];

                            factionTypeBase.AppendFormat(
                                "			{0} : [\"{1}\", \"{2}\"]",
                                factionClass["id"], factionClass["sign"], factionClass["name"]
                            );

                            if (i < factionClassList.Count - 1)
                                factionTypeBase.AppendLine(",");
                        }

                        factionTypeBase.Append(
@"
		};

        //格式 => id : [sign, name]
		public static const FactionJobs : Object = {
");

                        for (int i = 0; i < factionJobList.Count; i++)
                        {
                            Hashtable factionJob = factionJobList[i];

                            factionTypeBase.AppendFormat(
                                "			{0} : [\"{1}\", \"{2}\"]",
                                factionJob["id"], factionJob["sign"], factionJob["name"]
                            );

                            if (i < factionJobList.Count - 1)
                                factionTypeBase.AppendLine(",");
                        }

                        factionTypeBase.Append(
@"
		};

        //格式 => id : [sign, name]
		public static const FactionLevels : Object = {
");

                        for (int i = 0; i < factionLevelList.Count; i++)
                        {
                            Hashtable factionLevel = factionLevelList[i];

                            factionTypeBase.AppendFormat(
                                "			{0} : [\"{1}\", \"{2}\"]",
                                factionLevel["id"], factionLevel["sign"], factionLevel["faction_level_name"]
                            );

                            if (i < factionLevelList.Count - 1)
                                factionTypeBase.AppendLine(",");
                        }

                        factionTypeBase.Append(
    @"
        };
	}
}");

                        using (StreamWriter writer = new StreamWriter(Path.Combine(dir, "FactionTypeBase.as"), false))
                        {
                            writer.Write(factionTypeBase.ToString());
                        }
                    }
                    #endregion


                    #region New

                    StringBuilder asCode = new StringBuilder(1024);

                    asCode.Append(
@"package com.assist.server
{
	public class SystemData
	{
");
                    #region Mission

                    {
                        List<Hashtable> town = ExecuteHashList(connection, "SELECT *  FROM `town`");

                        GenerateFlashDataMapping("town", "id", town, asCode, false);


                        List<Hashtable> mission_section = ExecuteHashList(connection, "SELECT * FROM `mission_section`");

                        GenerateFlashDataMapping("mission_section", "id", mission_section, asCode, false);


                        List<Hashtable> mission = ExecuteHashList(connection, "SELECT * FROM `mission`");

                        GenerateFlashDataMapping("mission", "id", mission, asCode, false);


                        List<Hashtable> item_type = ExecuteHashList(connection, "SELECT * FROM `item_type`");

                        GenerateFlashDataMapping("item_type", "id", item_type, asCode, false);


                        List<Hashtable> item = ExecuteHashList(connection, "SELECT * FROM `item`");

                        GenerateFlashDataMapping("item", "id", item, asCode, false);


                        List<Hashtable> get_mission_section_by_town_id = new List<Hashtable>();

                        for (int i = 0; i < town.Count; i++)
                        {
                            List<Hashtable> list = ExecuteHashList(connection, "SELECT id FROM mission_section WHERE town_id = " + town[i]["id"]);

                            if (list.Count == 0)
                                continue;

                            Hashtable newItem = new Hashtable();

                            newItem.Add("town_id", town[i]["id"]);

                            foreach (Hashtable sectionId in list)
                            {
                                newItem.Add(sectionId["id"].ToString(), sectionId["id"]);
                            }

                            get_mission_section_by_town_id.Add(newItem);
                        }

                        asCode.AppendLine("        //城镇 => 剧情");

                        GenerateFlashDataMapping("get_mission_section_by_town_id", "town_id", get_mission_section_by_town_id, asCode, true);


                        List<Hashtable> get_mission_by_mission_section_id = new List<Hashtable>();

                        for (int i = 0; i < mission_section.Count; i++)
                        {
                            List<Hashtable> list = ExecuteHashList(connection, "SELECT id FROM mission WHERE mission_section_id = " + mission_section[i]["id"]);

                            if (list.Count == 0)
                                continue;

                            Hashtable newItem = new Hashtable();

                            newItem.Add("mission_section_id", mission_section[i]["id"]);

                            foreach (Hashtable missionId in list)
                            {
                                newItem.Add(missionId["id"].ToString(), missionId["id"]);
                            }

                            get_mission_by_mission_section_id.Add(newItem);
                        }

                        asCode.AppendLine("        //剧情 => 副本");

                        GenerateFlashDataMapping("get_mission_by_mission_section_id", "mission_section_id", get_mission_by_mission_section_id, asCode, true);


                        List<Hashtable> get_item_by_mission_id = new List<Hashtable>();

                        for (int i = 0; i < mission.Count; i++)
                        {
                            List<Hashtable> list = ExecuteHashList(connection, "SELECT item_id FROM mission_item WHERE mission_id = " + mission[i]["id"]);

                            if (list.Count == 0)
                                continue;

                            Hashtable newItem = new Hashtable();

                            newItem.Add("mission_id", mission[i]["id"]);

                            foreach (Hashtable itemId in list)
                            {
                                newItem.Add(itemId["item_id"].ToString(), itemId["item_id"]);
                            }

                            get_item_by_mission_id.Add(newItem);
                        }

                        asCode.AppendLine("        //副本 => 奖励物品");

                        GenerateFlashDataMapping("get_item_by_mission_id", "mission_id", get_item_by_mission_id, asCode, true);
                    }

                    #endregion

                    asCode.Append(
@"
    }
}");

                    using (StreamWriter writer = new StreamWriter(Path.Combine(dir, "SystemData.as"), false))
                    {
                        writer.Write(asCode.ToString());
                    }
                    #endregion

                    connection.Clone();
                }

                Info("客户端数据库映射代码生成完毕");

                return true;
            }
            catch (Exception ex)
            {
                Error("客户端数据库映射代码生成出错：" + ex.Message);

                return false;
            }
        }

        private static List<Hashtable> ExecuteHashList(MySqlConnection connection, string sql)
        {
            List<Hashtable> list = new List<Hashtable>();

            using (MySqlCommand command = new MySqlCommand(sql, connection))
            {
                using (MySqlDataReader reader = command.ExecuteReader())
                {
                    while (reader.Read())
                    {
                        Hashtable item = new Hashtable();

                        for (int i = 0; i < reader.FieldCount; i++)
                        {
                            string name = reader.GetName(i);

                            Type type = reader.GetFieldType(i);

                            if (reader.IsDBNull(i))
                                item.Add(name, null);
                            else if (type == typeof(Int32))
                                item.Add(name, reader.GetInt32(i));
                            else if (type == typeof(String))
                                item.Add(name, reader.GetString(i));
                        }

                        list.Add(item);
                    }

                    reader.Close();
                }
            }

            return list;
        }

        private static void GenerateFlashDataMapping(string name, string key, List<Hashtable> list, StringBuilder code, bool isArray)
        {
            code.Append("        private static const _").Append(name).Append(" : Object = {").AppendLine();

            Type keyType = null;
            List<string> fields = new List<string>();

            for (int i = 0; i < list.Count; i++)
            {
                object keyValue = list[i][key];

                if (keyType == null)
                    keyType = keyValue.GetType();

                if (keyValue is string)
                    code.Append("            '").Append(keyValue).Append("' : [");
                else
                    code.Append("            ").Append(keyValue).Append(" : [");

                int n = 0;

                foreach (string field in list[i].Keys)
                {
                    if (field == key)
                        continue;

                    n += 1;

                    if (i == 0)
                        fields.Add(field);

                    object value = list[i][field];

                    if (value == null)
                        code.Append("null");
                    else if (value is string)
                        code.Append("'").Append(value).Append("'");
                    else
                        code.Append(value);

                    if (n < list[i].Keys.Count - 1)
                        code.Append(", ");
                }

                code.Append("]");

                if (i < list.Count - 1)
                    code.AppendLine(",");
            }

            code.AppendLine();
            code.AppendLine("        };");
            code.AppendLine();

            code.Append("        public static function ").Append(name).Append("(").Append(key).Append(":").Append(keyType == typeof(string) ? "String" : "Number").Append(") : ").Append(isArray ? "Array" : "Object").Append(" {").AppendLine();
            code.Append("            var value : Array = _").Append(name).Append("[").Append(key).Append("];").AppendLine().AppendLine();

            if (isArray)
            {
                code.Append("            return value;").AppendLine();
            }
            else
            {
                code.Append("            if (value == null) return null;").AppendLine().AppendLine();
                code.Append("            return {").AppendLine();

                code.Append("                ").Append(key).Append(" : ").Append(key).AppendLine(",");

                for (int i = 0; i < fields.Count; i++)
                {
                    code.Append("                ").Append(fields[i]).Append(" : value[").Append(i).Append("]");

                    if (i < fields.Count - 1)
                        code.Append(",");

                    code.AppendLine();
                }

                code.Append("            };").AppendLine();
            }

            code.Append("        }").AppendLine();
            code.AppendLine();
        }

        public static bool GenerateErlangCode2(bool isConsole)
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

            StringBuilder router = new StringBuilder();

            router.Append("-module(game_router).").AppendLine();
            router.Append("-export([route_request/2]).").AppendLine().AppendLine();
            router.Append("route_request(<<Module:8, Action:8, Args/binary>>, State) -> ").AppendLine();
            router.Append("    catch route_request(Module, Action, Args, State).").AppendLine().AppendLine();

            int n = 0;

            int headIndex = 0;

            foreach (ProtoSpecModule module in moduleList)
            {
                router.Append("route_request(").Append(module.ModuleId).Append(", Action, _Args, State) -> ").AppendLine();
                router.Append("    case Action of").AppendLine();

                for (int i = 0; i < module.Actions.Count; i++)
                {
                    ProtoSpecAction action = module.Actions[i];

                    router.Append("        ").Append(action.ActionId).Append(" -> ").AppendLine();

                    if (action.Input.Columns.Count > 0)
                    {
                        router.Append("            <<");

                        int n2 = 0;
                        int lenCount = 1;

                        foreach (ProtoSpecColumn column in action.Input.Columns)
                        {
                            switch (column.ColumnType)
                            {
                                case ProtoSpecColumnType.Byte:
                                case ProtoSpecColumnType.Enum:
                                    router.Append(FormatName(column.Name)).Append(":8");
                                    break;
                                case ProtoSpecColumnType.Short:
                                    router.Append(FormatName(column.Name)).Append(":16");
                                    break;
                                case ProtoSpecColumnType.Int:
                                    router.Append(FormatName(column.Name)).Append(":32");
                                    break;
                                case ProtoSpecColumnType.Long:
                                    router.Append(FormatName(column.Name)).Append(":64");
                                    break;
                                case ProtoSpecColumnType.String:
                                    router.Append("Len").Append(lenCount).Append(":16, ").Append(FormatName(column.Name)).Append(":Len").Append(lenCount).Append("/binary");

                                    lenCount += 1;
                                    break;
                            }

                            if (n2 < action.Input.Columns.Count - 1)
                                router.Append(", ");

                            n2 += 1;
                        }

                        router.Append(">> = _Args,").AppendLine();
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

                    router.Append("State)");

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
                            false
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

                using (StreamWriter writer = new StreamWriter(Path.Combine(includeDir2, "gen\\api_" + module.Name + ".hrl"), false))
                {
                    writer.Write(head.ToString());

                    writer.WriteLine();
                }

                using (StreamWriter writer = new StreamWriter(Path.Combine(serverDir, "src\\gen\\api_" + module.Name + "_out.erl"), false))
                {
                    writer.Write(code.ToString().Substring(headIndex));
                }

                n += 1;
            }

            using (StreamWriter writer = new StreamWriter(Path.Combine(serverDir, "src\\gen\\game_router.erl"), false))
            {
                writer.Write(router.ToString());
            }

            Info("服务端代码生成完毕");

            return true;
        }

        private static string GenerateErlangFunction2(string moduleID, string actionID, string name, ProtoSpecSubset format, ref int autoFunCount, ref List<string> autoFunList, ref int level, bool islist)
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

                GenerateErlangVarBin2(paramName, column, ref columnBinDecl[i], ref columnBinCode[i], ref autoFunCount, ref autoFunList, ref level);
            }

            code.AppendLine("}) ->");

            for (int i = 0; i < columnBinDecl.Length; i++)
            {
                if (columnBinDecl[i] == null)
                    continue;

                code.AppendLine(columnBinDecl[i]);

                code.AppendLine();
            }

            code.AppendLine("    <<");

            if (moduleID != null)
            {
                code.Append("        ").Append(moduleID).AppendLine(":8,");
                code.Append("        ").Append(actionID).AppendLine(":8,");
            }

            for (int i = 0; i < columnBinCode.Length; i++)
            {
                code.Append("        ").Append(columnBinCode[i]);

                if (i < columnBinCode.Length - 1)
                    code.AppendLine(",");
                else
                    code.AppendLine();
            }

            code.Append("    >>");

            code.AppendLine(".");

            return code.ToString();
        }

        private static void GenerateErlangVarBin2(string name, ProtoSpecColumn column, ref string decl, ref string code, ref int autoFunCount, ref List<string> autoFunList, ref int level)
        {
            string tab = "";

            for (int i = 0; i < level; i++)
            {
                tab += "    ";
            }

            switch (column.ColumnType)
            {
                case ProtoSpecColumnType.Byte:
                case ProtoSpecColumnType.Enum:
                    code = name + ":8";
                    break;
                case ProtoSpecColumnType.Short:
                    code = name + ":16";
                    break;
                case ProtoSpecColumnType.Int:
                    code = name + ":32";
                    break;
                case ProtoSpecColumnType.Long:
                    code = name + ":64";
                    break;
                case ProtoSpecColumnType.String:
                    decl = string.Format("{1}Bin_{0} = list_to_binary({0}), \r\n{1}Bin_{0}_Len = size(Bin_{0}),", name, tab);

                    code = string.Format("Bin_{0}_Len:16, Bin_{0}/binary", name);
                    break;
                case ProtoSpecColumnType.List:
                    string funName = "item_to_bin_" + autoFunCount.ToString();

                    string tempVar = name + "_Item";

                    decl = string.Format("{3}BinList_{0} = [\r\n{3}    {1}({2}) || {2} <- {0}\r\n{3}], \r\n\r\n{3}{0}_Len = length({0}), \r\n{3}Bin_{0} = list_to_binary(BinList_{0}),", name, funName, tempVar, tab);

                    code = string.Format("{0}_Len:16, Bin_{0}/binary", name);

                    autoFunCount += 1;

                    autoFunList.Add(funName);

                    string funBody = GenerateErlangFunction2(null, null, funName, column.Format, ref autoFunCount, ref autoFunList, ref level, true);

                    autoFunList[autoFunList.IndexOf(funName)] = funBody;
                    break;
            }
        }

        public static bool GenerateErlangDatabaseCode2(bool isConsole, string server, string uid, string pwd, string database, string port)
        {
            try
            {
                string connectionString = "Server=" + server + ";Uid=" + uid + ";Pwd=" + pwd + ";Port=" + port + ";Database=information_schema;";

                using (MySqlConnection connection = new MySqlConnection(connectionString))
                {
                    connection.Open();

#if DEBUG
                    string databaseDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\database"));
                    string serverDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new"));
#else
					string databaseDir = Path.Combine(Environment.CurrentDirectory, "database");
					string serverDir = Path.Combine(Environment.CurrentDirectory, "server-new");
#endif

                    string indexFile = Path.Combine(databaseDir, "index.txt");

                    string headFile = Path.Combine(serverDir, "include\\gen\\game_db.hrl");
                    string codeFile = Path.Combine(serverDir, "src\\gen\\game_db.erl");
                    string game_db_init_file = Path.Combine(serverDir, "src\\gen\\game_db_init.erl");
                    string game_db_sync_file = Path.Combine(serverDir, "src\\gen\\game_db_sync.erl");
                    string game_db_admin_file = Path.Combine(serverDir, "src\\gen\\game_db_admin.erl");

                    #region GetTables

                    List<string> tableNameList = new List<string>();

                    using (MySqlCommand command = new MySqlCommand("SELECT `TABLE_NAME` FROM `TABLES` WHERE `TABLE_SCHEMA` = '" + database + "'", connection))
                    {
                        using (MySqlDataReader reader = command.ExecuteReader())
                        {
                            while (reader.Read())
                            {
                                tableNameList.Add(reader.GetString("TABLE_NAME"));
                            }

                            reader.Close();
                        }
                    }

                    tableNameList.Remove("db_version");

                    string[] tableList = tableNameList.ToArray();

                    #endregion

                    List<TableInfo2> tables = new List<TableInfo2>();

                    StringBuilder head = new StringBuilder();

                    #region macro

                    string connectionString2 = "Server=" + server + ";Uid=" + uid + ";Pwd=" + pwd + ";Port=" + port + ";Database=" + database + ";";

                    using (MySqlConnection connection2 = new MySqlConnection(connectionString2))
                    {
                        connection2.Open();

                        List<string> lines = new List<string>();
                        List<string> comments = new List<string>();

                        using (MySqlCommand command = new MySqlCommand("SELECT `lock` FROM `mission_section` ORDER BY `lock` ASC LIMIT 1;", connection2))
                        {
                            int missionLock = Convert.ToInt32(command.ExecuteScalar());

                            lines.Add("-define(INI_SECTION_KEY, " + missionLock + ").");
                            comments.Add("% 玩家初始剧情解锁权限");
                        }

                        using (MySqlCommand command = new MySqlCommand("SELECT `lock` FROM `mission` ORDER BY `lock` ASC LIMIT 1;", connection2))
                        {
                            int missionLock = Convert.ToInt32(command.ExecuteScalar());

                            lines.Add("-define(INI_MISSION_KEY, " + missionLock + "). ");
                            comments.Add("% 玩家初始副本解锁权限");
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM research_data_type ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(RDT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 奇术数据类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM game_function ORDER BY `lock` ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(FUN_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("lock") + ").");
                                    comments.Add("% 功能解锁权限 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        int maxlength = 0;

                        for (int i = 0; i < lines.Count; i++)
                        {
                            if (maxlength < lines[i].Length)
                                maxlength = lines[i].Length;
                        }

                        for (int i = 0; i < lines.Count; i++)
                        {
                            if (lines[i] == "")
                            {
                                head.AppendLine();
                                continue;
                            }

                            head.Append(lines[i]);

                            head.Append(GenerateSpace(maxlength + 4, lines[i].Length));

                            head.Append(comments[i]).AppendLine();
                        }

                        connection2.Close();
                    }

                    head.AppendLine();

                    #endregion

                    #region -record()

                    foreach (string table in tableList)
                    {
                        TableInfo2 info = GetTableInfo2(isConsole, connection, database, table, "row_key".Length, true);

                        if (info == null)
                            continue;

                        tables.Add(info);

                        head.Append("-record(").Append(info.name).Append(", {").AppendLine();

                        head.Append("    row_key,").AppendLine();

                        int i = 0;

                        foreach (string column in info.columns)
                        {
                            head.Append("    ").Append(column);

                            if (info.defaultValues[column] != null)
                            {
                                if (TableInfo2.IsNumericType(info.types[column]))
                                    head.Append(" = ").Append(info.defaultValues[column]);
                                else
                                    head.Append(" = \"").Append(info.defaultValues[column]).Append("\"");
                            }
                            else
                            {
                                head.Append(" = null");
                            }

                            if (i < info.columns.Count - 1)
                                head.Append(",");

                            string space = GenerateSpace(info.maxlength, column.Length);

                            if (info.comments[column] != string.Empty)
                                head.Append(space).Append("%% ").Append(info.comments[column]);

                            head.AppendLine();

                            i++;
                        }

                        head.Append("}).").AppendLine();

                        i = 0;

                        head.Append("-record(pk_").Append(info.name).Append(", {").AppendLine();

                        foreach (string column in info.primaryKeys)
                        {
                            head.Append("    ").Append(column);

                            if (i < info.primaryKeys.Count - 1)
                                head.AppendLine(",");
                            else
                                head.AppendLine();

                            i++;
                        }

                        head.Append("}).").AppendLine();

                        head.AppendLine();
                    }

                    #endregion

                    using (StreamWriter writer = new StreamWriter(headFile, false))
                    {
                        writer.Write(head.ToString());
                    }

                    #region game_db_init

                    StringBuilder game_db_init = new StringBuilder();

                    game_db_init.Append(@"-module(game_db_init).

-export([start/0, stop/0, init/0]).

-include(""game.hrl"").
-include(""gen/game_db.hrl"").

");
                    #region start/0

                    game_db_init.Append("start () ->").AppendLine();

                    game_db_init.Append("    mnesia:start(),").AppendLine();
                    game_db_init.Append("    mnesia:create_table(auto_increment, []).").AppendLine();

                    game_db_init.AppendLine();
                    game_db_init.AppendLine();

                    #endregion

                    #region stop/0

                    game_db_init.Append("stop () ->").AppendLine();
                    game_db_init.Append("    mnesia:stop().").AppendLine();

                    game_db_init.AppendLine();
                    game_db_init.AppendLine();

                    #endregion

                    #region init_db/0

                    game_db_init.AppendLine("init () ->");

                    foreach (TableInfo2 table in tables)
                    {
                        game_db_init.AppendFormat("    init({0})", table.name).AppendLine(",");
                    }

                    game_db_init.AppendLine();
                    game_db_init.AppendLine("    ?INFO(\"database init finished~n\", []).");

                    game_db_init.AppendLine();
                    game_db_init.AppendLine();

                    #endregion

                    int n = 0;

                    #region init/1

                    foreach (TableInfo2 table in tables)
                    {
                        game_db_init.Append("init (").Append(table.name).Append(") ->");

                        if (table.auto_increment != string.Empty)
                        {
                            game_db_init.AppendFormat(@"
    ?INFO(""game_db init: {0} start~n"", []),
	
    {{data, AutoIncResultId}} = mysql:fetch(gamedb, [<<
        ""SELECT IFNULL(MAX(`{1}`), 1) AS `max_id` FROM `{0}`;""
    >>]),

    [AutoIncResult] = lib_mysql:get_rows(AutoIncResultId),
    
    {{max_id, AutoIncStart}} = lists:keyfind(max_id, 1, AutoIncResult),

    AutoIncStart = mnesia:dirty_update_counter(auto_increment, {{{0}, {1}}}, AutoIncStart),
", table.name, table.auto_increment);
                        }
                        else
                        {
                            game_db_init.AppendFormat(@"
    ?INFO(""game_db init: {0} start~n"", []),
	
", table.name);
                        }

                        game_db_init.AppendFormat(@"
    {{data, CountResultId}} = mysql:fetch(gamedb, [<<
        ""SELECT COUNT(*) AS `count` FROM `{0}`;""
    >>]),

    [CountResult] = lib_mysql:get_rows(CountResultId),
    
    {{count, Count}} = lists:keyfind(count, 1, CountResult),
    
    {{atomic,ok}} = mnesia:create_table({0}, [
        {{record_name, {0}}}, {{attributes, record_info(fields, {0})}}
    ]),
    
    load({0}, 0, trunc(Count / 500) + 1)", table.name);

                        if (n < tables.Count - 1)
                            game_db_init.AppendLine(";");
                        else
                            game_db_init.AppendLine(".");

                        game_db_init.AppendLine();

                        n += 1;
                    }

                    #endregion

                    #region load/2

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        game_db_init.AppendFormat(@"load ({0}, MaxPage, MaxPage) -> 
    ?INFO(
        ""game_db init: {0} finished~n""
        ""===================================================================~n"", []
    ),
    ok;
load ({0}, Page, MaxPage) ->
    ?INFO(""game_db init: {0} (~p/~p)~n"", [Page + 1, MaxPage]),
	
    Offset = int_to_bin(Page * 500),
        
    {{data, ResultId}} = mysql:fetch(gamedb, [<<
        ""SELECT * FROM `{0}` LIMIT "", Offset/binary, "", 500;""
    >>]),

    Rows = lib_mysql:get_rows(ResultId),
    
    lists:foreach(
        fun(Row) ->
", table.name);

                        for (int i = 0; i < table.columns.Count; i++)
                        {
                            string column = table.columns[i];

                            string varName = FormatName(column);

                            string space = GenerateSpace(table.maxlength, varName.Length);

                            string space2 = GenerateSpace(table.maxlength, column.Length);

                            game_db_init.Append("            ");
                            game_db_init.AppendFormat("{{{1},{3}{0}}}{2}= lists:keyfind({1},{3}1, Row),", varName, column, space, space2);
                            game_db_init.AppendLine();
                        }

                        game_db_init.AppendFormat("\r\n            Record = #{0} {{", table.name).AppendLine();

                        game_db_init.AppendFormat("                row_key{0}= {{", GenerateSpace(table.maxlength, "row_key".Length));

                        for (int i = 0; i < table.primaryKeys.Count; i++)
                        {
                            game_db_init.AppendFormat("{0}", FormatName(table.primaryKeys[i]));

                            if (i < table.primaryKeys.Count - 1)
                                game_db_init.Append(", ");
                        }

                        game_db_init.Append("},").AppendLine();

                        for (int i = 0; i < table.columns.Count; i++)
                        {
                            string space = GenerateSpace(table.maxlength, table.columns[i].Length);

                            game_db_init.Append("                ");
                            game_db_init.AppendFormat("{0}{2}= {1}", table.columns[i], FormatName(table.columns[i]), space);

                            if (i < table.columns.Count - 1)
                                game_db_init.AppendLine(",");
                            else
                                game_db_init.AppendLine();
                        }

                        game_db_init.AppendLine("            },");

                        game_db_init.AppendFormat(@"
            Tran = fun() -> mnesia:write(Record) end,
            
            mnesia:transaction(Tran)
        end,
        Rows
    ),

    load({0}, Page + 1, MaxPage)", table.name);

                        if (n < tables.Count - 1)
                            game_db_init.AppendLine(";");
                        else
                            game_db_init.AppendLine(".");

                        game_db_init.AppendLine();

                        n += 1;
                    }
                    #endregion

                    game_db_init.Append(@"

int_to_bin (null) ->
    <<""NULL"">>;
int_to_bin (Value) ->
    list_to_binary(integer_to_list(Value)).
");

                    using (StreamWriter writer = new StreamWriter(game_db_init_file, false))
                    {
                        writer.Write(game_db_init.ToString());
                    }

                    #endregion

                    #region game_db

                    StringBuilder code = new StringBuilder();

                    code.Append(@"-module(game_db).
-export([
    dirty_select/2, 
    dirty_read/1, 
    select/2, 
    read/1, 
    write/1, 
    delete/1, 
    delete_select/2, 
    table/1, 
    table/2,
    do/1
]).

-include(""game.hrl"").
-include(""gen/game_db.hrl"").

-ifdef(debug).

-define(ENSURE_TRAN, ensure_tran()).

ensure_tran() -> case get(tran_action_list) of undefined -> exit(need_gamedb_tran); _ -> ok end.

-else.

-define(ENSURE_TRAN, ok).

-endif.
");

                    #region dirty_select/2
                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("dirty_select (").Append(table.name).Append(", MatchSpec) ->").AppendLine();
                        code.Append("    mnesia:dirty_select(").Append(table.name).Append(", MatchSpec)");

                        if (n < tables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }

                    #endregion

                    #region dirty_read/2
                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("dirty_read (#pk_").Append(table.name).Append("{");

                        for (int i = 0; i < table.primaryKeys.Count; i++)
                        {
                            code.Append(table.primaryKeys[i]).Append(" = ").Append(FormatName(table.primaryKeys[i]));

                            if (i < table.primaryKeys.Count - 1)
                                code.Append(", ");
                        }

                        code.Append("}) ->").AppendLine();

                        code.Append("    mnesia:dirty_read(").Append(table.name).Append(", {");

                        for (int i = 0; i < table.primaryKeys.Count; i++)
                        {
                            code.Append(FormatName(table.primaryKeys[i]));

                            if (i < table.primaryKeys.Count - 1)
                                code.Append(", ");
                        }

                        code.Append("})");

                        if (n < tables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }


                    #endregion

                    #region select/2
                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("select (").Append(table.name).Append(", MatchSpec) ->").AppendLine();
                        code.Append("    mnesia:dirty_select(").Append(table.name).Append(", MatchSpec)");

                        if (n < tables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }

                    #endregion

                    #region read/2
                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("read (#pk_").Append(table.name).Append("{");

                        for (int i = 0; i < table.primaryKeys.Count; i++)
                        {
                            code.Append(table.primaryKeys[i]).Append(" = ").Append(FormatName(table.primaryKeys[i]));

                            if (i < table.primaryKeys.Count - 1)
                                code.Append(", ");
                        }

                        code.Append("}) ->").AppendLine();

                        code.Append("    mnesia:dirty_read(").Append(table.name).Append(", {");

                        for (int i = 0; i < table.primaryKeys.Count; i++)
                        {
                            code.Append(FormatName(table.primaryKeys[i]));

                            if (i < table.primaryKeys.Count - 1)
                                code.Append(", ");
                        }

                        code.Append("})");

                        if (n < tables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }

                    #endregion

                    #region write/1
                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("write (Record) when is_record(Record, ").Append(table.name).Append(") -> ?ENSURE_TRAN,").AppendLine();
                        code.Append("    case Record #").Append(table.name).Append(".row_key of").AppendLine();
                        code.Append("        undefined ->").AppendLine();

                        #region insert

                        if (table.auto_increment != string.Empty)
                        {
                            code.Append("            validate_for_insert(Record),").AppendLine().AppendLine();
                            code.Append("            NewId = mnesia:dirty_update_counter(auto_increment, {").Append(table.name).Append(", ").Append(table.auto_increment).Append("}, 1),").AppendLine().AppendLine();
                            code.Append("            NewRecord = Record #").Append(table.name).Append("{").AppendLine();
                            code.Append("                ").Append(table.auto_increment).Append(" = NewId,").AppendLine();
                            code.Append("                row_key = {").AppendLine();

                            for (int i = 0; i < table.primaryKeys.Count; i++)
                            {
                                if (table.primaryKeys[i] == table.auto_increment)
                                    code.Append("                    NewId");
                                else
                                    code.Append("                    Record #").Append(table.name).Append(".").Append(table.primaryKeys[i]);

                                if (i < table.primaryKeys.Count - 1)
                                    code.Append(", ");

                                code.AppendLine();
                            }

                            code.Append("                }").AppendLine();
                            code.Append("            },").AppendLine().AppendLine();
                            code.Append("            mnesia:dirty_write(NewRecord),").AppendLine().AppendLine();
                            code.Append("            add_tran_log({insert, ").Append(table.name).Append(", NewRecord #").Append(table.name).Append(".row_key}),").AppendLine().AppendLine();
                            code.Append("            add_tran_action({").Append(table.name).Append(", insert, NewRecord}),").AppendLine().AppendLine();
                            code.Append("            {ok, NewRecord};").AppendLine();
                        }
                        else
                        {
                            code.Append("            validate_for_insert(Record),").AppendLine().AppendLine();
                            code.Append("            NewRecord = Record #").Append(table.name).Append("{").AppendLine();
                            code.Append("                row_key = {").AppendLine();

                            for (int i = 0; i < table.primaryKeys.Count; i++)
                            {
                                code.Append("                    Record #").Append(table.name).Append(".").Append(table.primaryKeys[i]);

                                if (i < table.primaryKeys.Count - 1)
                                    code.Append(", ");

                                code.AppendLine();
                            }

                            code.Append("                }").AppendLine();
                            code.Append("            },").AppendLine().AppendLine();
                            code.Append("            mnesia:dirty_write(NewRecord),").AppendLine().AppendLine();
                            code.Append("            add_tran_log({insert, ").Append(table.name).Append(", NewRecord #").Append(table.name).Append(".row_key}),").AppendLine().AppendLine();
                            code.Append("            add_tran_action({").Append(table.name).Append(", insert, NewRecord}),").AppendLine().AppendLine();
                            code.Append("            {ok, NewRecord};").AppendLine();
                        }

                        #endregion

                        code.AppendLine();

                        code.Append("        _ ->").AppendLine();

                        #region update

                        code.Append("            validate_for_update(Record),").AppendLine().AppendLine();
                        code.Append("            OldRecord = mnesia:dirty_read(").Append(table.name).Append(", Record #").Append(table.name).Append(".row_key),").AppendLine().AppendLine();
                        code.Append("            add_tran_log({update").Append(", OldRecord}),").AppendLine().AppendLine();
                        code.Append("            mnesia:dirty_write(Record),").AppendLine().AppendLine();
                        code.Append("            add_tran_action({").Append(table.name).Append(", update, Record}),").AppendLine().AppendLine();
                        code.Append("            {ok, Record}").AppendLine();

                        #endregion


                        code.Append("    end");

                        if (n < tables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }

                    #endregion

                    #region delete/1
                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("delete (#").Append(table.name).Append("{row_key = RowKey} = Record) -> ?ENSURE_TRAN,").AppendLine();
                        code.Append("    OldRecord = mnesia:dirty_read(").Append(table.name).Append(", Record #").Append(table.name).Append(".row_key),").AppendLine();
                        code.Append("    add_tran_log({delete").Append(", OldRecord}),").AppendLine().AppendLine();
                        code.Append("    mnesia:dirty_delete({").Append(table.name).Append(", RowKey}),").AppendLine();
                        code.Append("    add_tran_action({").Append(table.name).Append(", delete, Record}),").AppendLine().AppendLine();
                        code.Append("    ok");

                        if (n < tables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }

                    #endregion

                    #region delete_select/2
                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("delete_select (").Append(table.name).Append(", MatchSpec) -> ?ENSURE_TRAN,").AppendLine();
                        code.Append("    case mnesia:dirty_select(").Append(table.name).Append(", MatchSpec) of").AppendLine();
                        code.Append("        [] -> {ok, 0};").AppendLine();
                        code.Append("        Rows when is_list(Rows) ->").AppendLine();
                        code.Append("            Num = lists:foldl(fun(Row, Count) ->").AppendLine();
                        code.Append("                delete(Row),").AppendLine();
                        code.Append("                Count + 1").AppendLine();
                        code.Append("            end, 0, Rows),").AppendLine();
                        code.Append("            {ok, Num}").AppendLine();
                        code.Append("    end");

                        if (n < tables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }


                    #endregion

                    #region table/1

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("table (").Append(table.name).Append(") -> mnesia:table(").Append(table.name).Append(")");

                        if (n == tables.Count - 1)
                            code.AppendLine(".").AppendLine().AppendLine();
                        else
                            code.AppendLine(";");

                        n += 1;
                    }

                    #endregion

                    #region table/2

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("table (").Append(table.name).Append(", Options) -> mnesia:table(").Append(table.name).Append(", Options)");

                        if (n == tables.Count - 1)
                            code.AppendLine(".").AppendLine().AppendLine();
                        else
                            code.AppendLine(";");

                        n += 1;
                    }

                    #endregion

                    #region validate_for_insert/1

                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("validate_for_insert (Record) when is_record(Record, ").Append(table.name).Append(") ->").AppendLine();

                        foreach (string column in table.columns)
                        {
                            if (column == table.auto_increment || table.is_nullable[column])
                                continue;

                            code.Append("    if Record #").Append(table.name).Append(".").Append(column).Append(" == null -> throw({null_column, insert, ").Append(table.name).Append(", ").Append(column).Append("}); true -> ok end,").AppendLine();
                        }

                        code.Append("    ok");

                        if (n < tables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }

                    #endregion

                    #region validate_for_update/1

                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("validate_for_update (Record) when is_record(Record, ").Append(table.name).Append(") ->").AppendLine();

                        foreach (string column in table.columns)
                        {
                            if (table.is_nullable[column])
                                continue;

                            code.Append("    if Record #").Append(table.name).Append(".").Append(column).Append(" == null -> throw({null_column, update, ").Append(table.name).Append(", ").Append(column).Append("}); true -> ok end,").AppendLine();
                        }

                        code.Append("    ok");

                        if (n < tables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }

                    #endregion

                    code.Append(
@"

do (Tran) ->
	case get(tran_action_list) of
        undefined ->
            put(tran_log, []),
            put(tran_action_list, []),

            case catch Tran() of
                {'EXIT', {aborted, Reason}} -> 
                    rollback(get(tran_log)),
                    erase(tran_log),
                    erase(tran_action_list),
                    exit(Reason);

                {'EXIT', Reason} -> 
                    rollback(get(tran_log)),
                    erase(tran_log),
                    erase(tran_action_list),
                    exit(Reason);
                    
                Result ->
                    erase(tran_log),
                    TranActionList = erase(tran_action_list),
                    
                    case TranActionList of
                        [] -> {atomic, Result};
                         _ -> game_db_sync_proc ! {sync, TranActionList}, {atomic, Result}
                    end
            end;
    
        _ -> 
			{atomic, Tran()}
	end.

    
add_tran_log (Data) ->
    TranLogList = get(tran_log),
    put(tran_log, [Data | TranLogList]).


add_tran_action (TranAction) ->
    TranActionList = get(tran_action_list),
    put(tran_action_list, [TranAction | TranActionList]).


rollback ([]) ->
    ok;
rollback ([Data | Term]) ->
    case Data of
        {insert, Table, RowKey} ->
            mnesia:dirty_delete({Table, RowKey});
        {update, Row} ->
            mnesia:dirty_write(Row);
        {delete, Row} ->
            mnesia:dirty_write(Row)
    end,
    rollback(Term).
");

                    using (StreamWriter writer = new StreamWriter(codeFile, false))
                    {
                        writer.Write(code.ToString());
                    }

                    #endregion

                    #region game_db_sync

                    StringBuilder game_db_sync = new StringBuilder();

                    game_db_sync.Append(@"-module(game_db_sync).

-export([start_link/0, sync_proc_init/1]).

-include(""game.hrl"").
-include(""gen/game_db.hrl"").

");
                    #region tran_action_to_sql/1

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        int n2 = 0;

                        #region insert

                        game_db_sync.Append("tran_action_to_sql ({").Append(table.name).Append(", insert, Record}) ->").AppendLine();

                        foreach (string column in table.columns)
                        {
                            game_db_sync.Append("    ");

                            string space = GenerateSpace(table.maxlength, FormatName(column).Length);

                            if (TableInfo2.IsNumericType(table.types[column]))
                                game_db_sync.AppendFormat("{0}{3}= int_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);
                            else
                                game_db_sync.AppendFormat("{0}{3}= lst_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);

                            game_db_sync.AppendLine();
                        }

                        game_db_sync.AppendLine();

                        game_db_sync.AppendFormat(@"    <<
    ""INSERT INTO `{0}` SET """, table.name);

                        n2 = 0;

                        foreach (string column in table.columns)
                        {
                            game_db_sync.AppendLine();
                            game_db_sync.Append("    ");

                            if (n2 != 0)
                                game_db_sync.Append("\",");
                            else
                                game_db_sync.Append("\"");

                            game_db_sync.AppendFormat("`{0}` = \", {1}/binary, ", column, FormatName(column));

                            n2 += 1;
                        }

                        game_db_sync.AppendFormat(@""";""
    >>;");

                        game_db_sync.AppendLine();
                        game_db_sync.AppendLine();

                        #endregion

                        #region delete

                        game_db_sync.Append("tran_action_to_sql ({").Append(table.name).Append(", delete, Record}) ->").AppendLine();

                        foreach (string primaryKey in table.primaryKeys)
                        {
                            game_db_sync.Append("    ");
                            game_db_sync.AppendFormat("{0} = int_to_bin(Record #{1}.{2}),", FormatName(primaryKey), table.name, primaryKey);

                            game_db_sync.AppendLine();
                            game_db_sync.AppendLine();
                        }

                        game_db_sync.Append("");

                        game_db_sync.AppendFormat(@"    <<
    ""DELETE FROM `{0}` WHERE"", ", table.name);

                        n2 = 0;

                        foreach (string primaryKey in table.primaryKeys)
                        {
                            game_db_sync.AppendLine();
                            game_db_sync.Append("    ");

                            game_db_sync.AppendFormat("\"`{0}` = \", {1}/binary, ", primaryKey, FormatName(primaryKey));

                            if (n2 < table.primaryKeys.Count - 1)
                                game_db_sync.Append("\" AND\",");

                            n2 += 1;
                        }

                        game_db_sync.AppendLine(@""";""
    >>;");

                        game_db_sync.AppendLine();
                        game_db_sync.AppendLine();

                        #endregion

                        #region update

                        game_db_sync.Append("tran_action_to_sql ({").Append(table.name).Append(", update, Record}) ->").AppendLine();

                        foreach (string column in table.columns)
                        {
                            game_db_sync.Append("    ");

                            string space = GenerateSpace(table.maxlength, FormatName(column).Length);

                            if (TableInfo2.IsNumericType(table.types[column]))
                                game_db_sync.AppendFormat("{0}{3}= int_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);
                            else
                                game_db_sync.AppendFormat("{0}{3}= lst_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);

                            game_db_sync.AppendLine();
                        }

                        game_db_sync.AppendLine();

                        game_db_sync.AppendFormat(@"    <<
    ""UPDATE `{0}` SET "",", table.name);

                        n2 = 0;

                        foreach (string column in table.columns)
                        {
                            if (column == table.auto_increment)
                                continue;

                            game_db_sync.AppendLine();

                            if (n2 != 0)
                                game_db_sync.Append("    \",");
                            else
                                game_db_sync.Append("    \"");

                            game_db_sync.AppendFormat("`{0}` = \", {1}/binary, ", column, FormatName(column));


                            n2 += 1;
                        }

                        game_db_sync.AppendFormat(@"
    "" WHERE "", ");

                        n2 = 0;

                        foreach (string primaryKey in table.primaryKeys)
                        {
                            game_db_sync.AppendLine();
                            game_db_sync.Append("    ");

                            game_db_sync.AppendFormat("\"`{0}` = \", {1}/binary, ", primaryKey, FormatName(primaryKey));

                            if (n2 < table.primaryKeys.Count - 1)
                                game_db_sync.Append("\" AND\", ");

                            n2 += 1;
                        }

                        game_db_sync.Append(@""";""
    >>");

                        #endregion

                        if (n < tables.Count - 1)
                            game_db_sync.AppendLine(";");
                        else
                            game_db_sync.AppendLine(".");

                        game_db_sync.AppendLine();
                        game_db_sync.AppendLine();

                        n += 1;
                    }

                    #endregion

                    game_db_sync.Append(
@"

start_link () ->
    proc_lib:start_link(?MODULE, sync_proc_init, [self()]).
    
sync_proc_init (Parent) ->
    register(game_db_sync_proc, self()),
    proc_lib:init_ack(Parent, {ok, self()}),
    sync_proc_loop().
    
sync_proc_loop () ->
    receive
        {sync, TranActionList} ->
            case catch mysql:fetch(gamedb, [tran_action_list_to_sql_list(TranActionList)]) of
                {'EXIT', Reason} ->
                    ?ERROR(""game_db sync failed:~p~n"", [Reason]),
                    sync_proc_loop();
                _ ->
                    sync_proc_loop()
            end
    end.
    

tran_action_list_to_sql_list (TranActions) ->
    tran_action_list_to_sql_list(TranActions, []).
    
tran_action_list_to_sql_list ([], SqlList) ->
    SqlList;
tran_action_list_to_sql_list ([TranAction|Tail], SqlList) ->
    Sql = tran_action_to_sql(TranAction),
    tran_action_list_to_sql_list(Tail, [Sql | SqlList]).
    
    
lst_to_bin (null) ->
	<<""NULL"">>;
lst_to_bin (List) ->
	Bin = list_to_binary(List),
	<<""'"", Bin/binary, ""'"">>.
	
int_to_bin (null) ->
    <<""NULL"">>;
int_to_bin (Value) ->
    list_to_binary(integer_to_list(Value)).
");

                    using (StreamWriter writer = new StreamWriter(game_db_sync_file, false))
                    {
                        writer.Write(game_db_sync.ToString());
                    }

                    #endregion

                    #region game_db_admin

                    StringBuilder game_db_admin = new StringBuilder();

                    game_db_admin.Append(@"-module(game_db_admin).
-export([
    refresh_player/1
]).

-include(""game.hrl"").
-include(""gen/game_db.hrl"").

");

                    #region refresh_player/1

                    game_db_admin.Append("refresh_player (PlayerId) ->\r\n");

                    int playerTableCount = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        if (table.name.StartsWith("player_") == false)
                            continue;

                        game_db_admin.AppendFormat("    refresh({0}, PlayerId)", table.name).AppendLine(",");

                        playerTableCount += 1;
                    }

                    game_db_admin.AppendLine();
                    game_db_admin.AppendLine("    ?INFO(\"refresh player ~p finished~n\", [PlayerId]).");

                    game_db_admin.AppendLine();
                    game_db_admin.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        if (table.name.StartsWith("player_") == false)
                            continue;

                        game_db_admin.AppendFormat(@"
refresh ({0}, ThePlayerId) ->
    ?INFO(""game_db: refresh player_data for player ~p~n"", [ThePlayerId]),
	
    ThePlayerIdBin = int_to_bin(ThePlayerId),
        
    {{data, ResultId}} = mysql:fetch(gamedb, [<<
        ""SELECT * FROM `{0}` WHERE `player_id` = "", ThePlayerIdBin/binary, "";""
    >>]),

    Rows = lib_mysql:get_rows(ResultId),
    
    lists:foreach(
        fun(Row) ->
", table.name);

                        for (int i = 0; i < table.columns.Count; i++)
                        {
                            string column = table.columns[i];

                            string varName = FormatName(column);

                            string space = GenerateSpace(table.maxlength, varName.Length);

                            string space2 = GenerateSpace(table.maxlength, column.Length);

                            game_db_admin.Append("            ");
                            game_db_admin.AppendFormat("{{{1},{3}{0}}}{2}= lists:keyfind({1},{3}1, Row),", varName, column, space, space2);
                            game_db_admin.AppendLine();
                        }

                        game_db_admin.AppendFormat("\r\n            Record = #{0} {{", table.name).AppendLine();

                        game_db_admin.AppendFormat("                row_key{0}= {{", GenerateSpace(table.maxlength, "row_key".Length));

                        for (int i = 0; i < table.primaryKeys.Count; i++)
                        {
                            game_db_admin.AppendFormat("{0}", FormatName(table.primaryKeys[i]));

                            if (i < table.primaryKeys.Count - 1)
                                game_db_admin.Append(", ");
                        }

                        game_db_admin.Append("},").AppendLine();

                        for (int i = 0; i < table.columns.Count; i++)
                        {
                            string space = GenerateSpace(table.maxlength, table.columns[i].Length);

                            game_db_admin.Append("                ");
                            game_db_admin.AppendFormat("{0}{2}= {1}", table.columns[i], FormatName(table.columns[i]), space);

                            if (i < table.columns.Count - 1)
                                game_db_admin.AppendLine(",");
                            else
                                game_db_admin.AppendLine();
                        }

                        game_db_admin.AppendLine("            },");

                        game_db_admin.AppendFormat(@"
            Tran = fun() -> mnesia:write(Record) end,
            
            mnesia:transaction(Tran)
        end,
        Rows
    )", table.name);

                        if (n < playerTableCount - 1)
                            game_db_admin.AppendLine(";");
                        else
                            game_db_admin.AppendLine(".");

                        game_db_admin.AppendLine();

                        n += 1;
                    }

                    #endregion

                    game_db_admin.Append(@"

int_to_bin (null) ->
    <<""NULL"">>;
int_to_bin (Value) ->
    list_to_binary(integer_to_list(Value)).
");

                    using (StreamWriter writer = new StreamWriter(game_db_admin_file, false))
                    {
                        writer.Write(game_db_admin.ToString());
                    }

                    #endregion

                    connection.Clone();
                }

                Info("服务端数据库映射代码生成完毕");

                return true;
            }
            catch (Exception ex)
            {
                Error("服务器端数据库映射代码生成出错：" + ex.Message);

                return false;
            }

        }

        private static TableInfo2 GetTableInfo2(bool isConsole, MySqlConnection connection, string database, string table, int defaultMaxLen, bool getPrimarykeys)
        {
            int maxlength = defaultMaxLen;

            int maxlength2 = 0;

            string auto_increment = string.Empty;

            List<string> columns = new List<string>();

            List<string> primaryKeys = new List<string>();

            Dictionary<string, string> types = new Dictionary<string, string>();

            Dictionary<string, bool> is_nullable = new Dictionary<string, bool>();

            Dictionary<string, string> defaultValues = new Dictionary<string, string>();

            Dictionary<string, string> comments = new Dictionary<string, string>();

            string sql = @"
SELECT 
	`COLUMN_NAME`, `COLUMN_KEY`, `DATA_TYPE`, `EXTRA`, `COLUMN_DEFAULT`, `IS_NULLABLE`, `COLUMN_COMMENT`
FROM 
	`COLUMNS` 
WHERE 
	`TABLE_SCHEMA` = '" + database + "' AND TABLE_NAME = '" + table + "' ORDER BY ORDINAL_POSITION ASC;";

            using (MySqlCommand command = new MySqlCommand(sql, connection))
            {
                using (MySqlDataReader reader = command.ExecuteReader())
                {
                    while (reader.Read())
                    {
                        string column = reader.GetString("COLUMN_NAME");

                        columns.Add(column);

                        if (getPrimarykeys && reader.GetString("COLUMN_KEY") == "PRI")
                        {
                            primaryKeys.Add(column);

                            if (column.Length > maxlength2)
                                maxlength2 = column.Length;
                        }

                        if (column.Length > maxlength)
                            maxlength = column.Length;

                        if (reader.GetString("EXTRA") == "auto_increment")
                            auto_increment = column;

                        if (reader.IsDBNull(reader.GetOrdinal("COLUMN_DEFAULT")))
                            defaultValues.Add(column, null);
                        else
                            defaultValues.Add(column, reader.GetString("COLUMN_DEFAULT"));

                        if (reader.GetString("IS_NULLABLE") == "NO")
                            is_nullable.Add(column, false);
                        else
                            is_nullable.Add(column, true);

                        if (reader.IsDBNull(reader.GetOrdinal("COLUMN_COMMENT")))
                            comments.Add(column, string.Empty);
                        else
                            comments.Add(column, reader.GetString("COLUMN_COMMENT"));

                        types.Add(column, reader.GetString("DATA_TYPE"));
                    }
                }
            }

            if (getPrimarykeys && primaryKeys.Count == 0)
            {
                string message = "表" + table + "没有主键";

                Info(message);

                return null;
            }

            TableInfo2 info = new TableInfo2();

            info.name = table;
            info.maxlength = maxlength;
            info.maxlength2 = maxlength2;
            info.types = types;
            info.columns = columns;
            info.primaryKeys = primaryKeys;
            info.is_nullable = is_nullable;
            info.defaultValues = defaultValues;
            info.auto_increment = auto_increment;
            info.comments = comments;

            return info;
        }

        private class TableInfo2
        {
            public string name;

            public int maxlength;

            public int maxlength2;

            public List<string> columns;

            public List<string> primaryKeys;

            public Dictionary<string, string> types;

            public Dictionary<string, bool> is_nullable;

            public Dictionary<string, string> defaultValues;

            public Dictionary<string, string> comments;

            public string auto_increment;

            public static bool IsNumericType(string type)
            {
                return type == "int";
            }
        }
    }
}
