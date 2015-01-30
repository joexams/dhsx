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
        public static bool GenerateFlashDatabaseCode2(bool isConsole, string server, string uid, string pwd, string database, string port)
        {
            try
            {
                /*
#if DEBUG
                string dir = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\client\\com\\assist\\server");
#else
				string dir = Path.Combine(Environment.CurrentDirectory, "client\\com\\assist\\server");
#endif
				dir = FixPath(dir);

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
                    (townNpcList.Length > 0 ? townNpcList.ToString(0, townNpcList.Length - (Environment.NewLine.Length + 1)) : ""),
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
        public static const BREAK : int = 1;
        public static const CONTINUE : int = 2;
        public static const MATCH_BREAK : int = 3;
        public static const MATCH_CONTINUE : int = 4;
        
");
                    #region Mission

                    {
                        List<Hashtable> town = ExecuteHashList(connection, "SELECT *  FROM `town`");

                        GenerateFlashDataMapping("town", "id", town, asCode, false);


                        List<Hashtable> mission_section = ExecuteHashList(connection, "SELECT * FROM `mission_section`");

                        GenerateFlashDataMapping("mission_section", "id", mission_section, asCode, false);


                        List<Hashtable> mission = ExecuteHashList(connection, "SELECT * FROM `mission`");

                        GenerateFlashDataMapping("mission", "id", mission, asCode, false);


                        //List<Hashtable> item_type = ExecuteHashList(connection, "SELECT * FROM `item_type`");

                        //GenerateFlashDataMapping("item_type", "id", item_type, asCode, false);


                        List<Hashtable> item = ExecuteHashList(connection, "SELECT * FROM `item`");

                        GenerateFlashDataMapping("item", "id", item, asCode, false);

                        //List<Hashtable> monster = ExecuteHashList(connection, "SELECT * FROM `monster`");

                        //GenerateFlashDataMapping("monster", "id", monster, asCode, false);


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
                */

                System.Diagnostics.Process proc = new System.Diagnostics.Process();

#if DEBUG
                proc.StartInfo.WorkingDirectory = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\tool\\ServerData");
#else
                proc.StartInfo.WorkingDirectory = Path.Combine(Environment.CurrentDirectory, "tool\\ServerData");
#endif
                proc.StartInfo.FileName = "php";
                proc.StartInfo.Arguments = "produce_all.php " + server + " " + port + " " + database + " " + uid + " " + pwd;
                proc.StartInfo.CreateNoWindow = true;
                proc.StartInfo.UseShellExecute = false;
                proc.StartInfo.RedirectStandardOutput = true;
                proc.StartInfo.RedirectStandardError = true;
                proc.Start();

                while (!proc.HasExited)
                {
                    //Console.WriteLine(proc.StandardOutput.ReadLine());
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
                        code.Append("'").Append(value.ToString().Replace("\r", "").Replace("\n", "\\n")).Append("'");
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

            if (!isArray)
            {
                code.Append("        public static function wrap_").Append(name).Append("_object(").Append(key).Append(":").Append(keyType == typeof(string) ? "String" : "Number").Append(", value:Array) : Object {").AppendLine();
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

                code.Append("        }").AppendLine();
                code.AppendLine();

                code.Append("        public static function select_").Append(name).Append("(callback:Function) : Array {").AppendLine();
                code.Append("            var result:Array = new Array();").AppendLine().AppendLine();
                code.Append("            for (var key:String in _").Append(name).Append(") {").AppendLine();
                code.Append("                var ").Append(key).Append(":").Append(keyType == typeof(string) ? "String" : "Number").Append(" = ").Append((keyType == typeof(string) ? "key;" : "parseInt(key);")).AppendLine();
                code.Append("                var obj:Object = wrap_").Append(name).Append("_object(").Append(key).Append(", _").Append(name).Append("[").Append(key).Append("]);").AppendLine().AppendLine();
                code.Append("                var s:int = callback(obj);").AppendLine().AppendLine();
                code.Append("                if (s == MATCH_CONTINUE || s == MATCH_BREAK) result.push(obj);").AppendLine();
                code.Append("                if (s == BREAK || s == MATCH_BREAK) break;").AppendLine();
                code.Append("            }").AppendLine().AppendLine();
                code.Append("            return result;").AppendLine();
                code.Append("        }").AppendLine();
                code.AppendLine();
            }

            code.Append("        public static function ").Append(name).Append("(").Append(key).Append(":").Append(keyType == typeof(string) ? "String" : "Number").Append(") : ").Append(isArray ? "Array" : "Object").Append(" {").AppendLine();
            code.Append("            var value : Array = _").Append(name).Append("[").Append(key).Append("];").AppendLine().AppendLine();

            if (isArray)
            {
                code.Append("            return value;").AppendLine();
            }
            else
            {
                code.Append("            return wrap_").Append(name).Append("_object(").Append(key).Append(", value);").AppendLine();
            }

            code.Append("        }").AppendLine();
            code.AppendLine();
        }

	}
}

