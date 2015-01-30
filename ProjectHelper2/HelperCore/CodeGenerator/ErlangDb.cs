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
        public static bool GenerateErlangDatabaseCode(bool isConsole, string server, string uid, string pwd, string database, string port, bool developMode)
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
                    databaseDir = FixPath(databaseDir);
                    serverDir = FixPath(serverDir);

                    string indexFile = Path.Combine(databaseDir, "index.txt");

                    string headFile = Path.Combine(serverDir, "include\\gen\\game_db.hrl");
                    string codeFile = Path.Combine(serverDir, "src\\gen\\game_db.erl");
                    string game_db_init_file = Path.Combine(serverDir, "src\\gen\\game_db_init.erl");
                    string game_db_sync_file = Path.Combine(serverDir, "src\\gen\\game_db_sync.erl");
                    string game_db_admin_file = Path.Combine(serverDir, "src\\gen\\game_db_admin.erl");
                    string game_db_dump_file = Path.Combine(serverDir, "src\\gen\\game_db_dump.erl");
                    string game_db_save_file = Path.Combine(serverDir, "src\\gen\\game_db_save.erl");

                    headFile = FixPath(headFile);
                    codeFile = FixPath(codeFile);
                    game_db_init_file = FixPath(game_db_init_file);
                    game_db_sync_file = FixPath(game_db_sync_file);
                    game_db_admin_file = FixPath(game_db_admin_file);
                    game_db_dump_file = FixPath(game_db_dump_file);
                    game_db_save_file = FixPath(game_db_save_file);

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

                        using (MySqlCommand command = new MySqlCommand("SELECT `lock` FROM `mission` WHERE `type` = 0 ORDER BY `lock` ASC LIMIT 1;", connection2))
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

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM item_type ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(ITT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 物品类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM npc_function ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(NF_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% NPC功能类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM npc ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(NPC_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% NPC模板数据 - " + reader.GetString("name"));
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
                                    lines.Add("-define(FUN_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 功能ID - " + reader.GetString("name"));
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
                                    lines.Add("-define(FUN_LOCK_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("lock") + ").");
                                    comments.Add("% 功能解锁权限 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT MAX(level) FROM role_job_level_data;", connection2))
                        {
                            int maxRoleLevel = Convert.ToInt32(command.ExecuteScalar());

                            lines.Add("-define(MAX_ROLE_LEVEL, " + maxRoleLevel + ").");
                            comments.Add("% 最大角色等级");
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM ingot_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(ICT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 元宝变动类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");


                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM item;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(I_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 物品标识 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM soul_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(SCR_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 灵件变动类型 - " + reader.GetString("description"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM attribute_stone_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(ASCT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 属性石变动类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM soul_stone_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(SSCR_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 灵石变动类型 - " + reader.GetString("description"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM item_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(ICR_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 物品变动类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM deploy_start_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(DST_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 阵盘星变动类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM spirit_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(SCT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 精魄变动类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM long_yu_ling_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(LYLT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 玩家龙鱼令变动记录 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM xian_ling_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(XLT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 玩家仙令变更类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM xianling_tree_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(XTT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 仙令树种子日志 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM elixir_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(ECT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 丹药变动类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM coin_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(CCT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 铜钱变动类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM fate_log_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(FLT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 猎命日志类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM fame_log_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(FLT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 声望日志类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM power_log_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(PLT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 体力日志类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM exp_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(ECT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 经验变动类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM skill_log_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(SLT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 阅历日志类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM coin_tree_count_log_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(CTC_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 仙露日志类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM state_point_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(SPCT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 境界点日志类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM marry_favor_log_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(MFLT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 结婚亲密度日志类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM marry_skill;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(MSS_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 结婚技能类型 - " + reader.GetString("desc"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM server_data_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(PSD_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 公用数据id类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM favor_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(FCT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 玩家伙伴好感度记录类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM delay_notify_message_template;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(DNM_" + reader.GetString("message_sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 感叹号消息模版 - " + reader.GetString("template_message").Replace("\r", "").Replace("\n", "\\r\\n"));
                                }

                                reader.Close();
                            }
                        }

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM super_gift_message_template;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(SGMT_" + reader.GetString("message_sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 大礼包消息模版 - " + reader.GetString("template_message").Replace("\n", " "));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM role_stunt_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(RST_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 战法类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM role_stunt;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(RS_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 战法 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM role_attack_range;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(RAR_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 攻击范围 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM role_job;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(RJ_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 角色职业 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM fate_quality;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(FQ_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 命格品质 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM achievement;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(ACH_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 成就标识 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM day_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(DT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 定期活动 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM super_gift_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(SGT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 大礼包标识 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM world_war_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(WWT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 群仙会战斗阶段类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM faction_gift_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(FGT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 帮派礼包类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM run_business_town;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(RBT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 跑商城镇 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM league_war_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(LWT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 仙盟争霸阶段类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM title;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(TIT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 称号 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM war_attribute_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(WAT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 战争属性类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM abnormal_record_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(ART_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 异常类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM charge_lottery_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(CLT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 充值抽奖操作类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM pet_animal_change_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(PACT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 宠物喂养 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM target_info;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(TI_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 目标信息表 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM week_ranking;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(WR_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 排行榜 - " + reader.GetString("desc"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM crystal_log_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(CRLT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 天晶变更类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM enhance_weapon_effect;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(EWE_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 神兵效果类型 - " + reader.GetString("description"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM feats_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(FT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 功勋变更类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM pearl_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(PRT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 元神珠变更类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM blood_pet_stunt;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(BPS_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 血契灵兽技能 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM blood_pet_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(BPT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 灵兽变更类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM blood_pet_chip_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(BPCT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 灵兽碎片变更类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM passivity_stunt;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(PST_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 被动技能类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM ba_xian_ling_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(BXLT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 八仙令变动类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM immortal_art_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(IAT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 仙奇术类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM consume_jifen_type;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(CJT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 消耗积分类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM dragonball_change_type ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(DCT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% DCT - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM dragonball_effect ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(DE_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% DE - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM dragonball_buff ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(DB_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% DB - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM dragonball_quality ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(DQ_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% DQ - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM dragonball ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(D_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% D - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM ling_yun_type ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(LYT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 灵蕴变更类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM neidan_log_type ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(NDT_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 内丹变更类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM dragon_egg_log_type ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(DET_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 龙蛋变更类型 - " + reader.GetString("name"));
                                }

                                reader.Close();
                            }
                        }

                        lines.Add("");
                        comments.Add("");

                        using (MySqlCommand command = new MySqlCommand("SELECT * FROM partners_invite_story_type ORDER BY id ASC;", connection2))
                        {
                            using (MySqlDataReader reader = command.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    lines.Add("-define(PIST_" + reader.GetString("sign").ToUpper() + ", " + reader.GetInt32("id") + ").");
                                    comments.Add("% 伙伴招募 剧情后接类型 - " + reader.GetString("name"));
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

                            if (i < info.columns.Count - 1 || developMode)
                                head.Append(",");

                            string space = GenerateSpace(info.maxlength, column.Length);

                            if (info.comments[column] != string.Empty)
                                head.Append(space).Append("%% ").Append(info.comments[column]);

                            head.AppendLine();

                            i++;
                        }

                        if (developMode)
                        {
                            head.Append("    row_ver = 0").AppendLine();
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

-export([init/0, wait_for_loaded/0]).

-include(""game.hrl"").
-include(""gen/game_db.hrl"").

");

                    #region init/0

                    game_db_init.AppendLine("init () ->");
                    game_db_init.AppendLine("    register(game_db, self()),");
                    game_db_init.AppendLine("    mysql:fetch(gamedb, [<<\"SET FOREIGN_KEY_CHECKS=0;\">>]),");
                    game_db_init.AppendLine("    ets:new(auto_increment, [public, set, named_table]),");

                    foreach (TableInfo2 table in tables)
                    {
                        game_db_init.AppendFormat("    init({0})", table.name).AppendLine(",");
                    }

                    game_db_init.AppendLine("    proc_lib:init_ack({ok, self()}),");
                    game_db_init.AppendLine();
                    game_db_init.AppendLine("    ?INFO(\"database init finished~n\", []),");
                    game_db_init.AppendLine();
                    game_db_init.AppendLine("    loop().");
                    game_db_init.AppendLine();

                    game_db_init.AppendLine(@"
loop () ->
    receive
        {is_loaded, Pid} -> Pid ! yes, loop();
        _ -> loop()
    end.

wait_for_loaded () ->
    game_db ! {is_loaded, self()},
    receive yes -> ok end.
");
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
        ""SELECT IFNULL(MAX(`{1}`), 0) AS `max_id` FROM `{0}`;""
    >>]),

    [AutoIncResult] = lib_mysql:get_rows(AutoIncResultId),
    
    {{max_id, AutoIncStart}} = lists:keyfind(max_id, 1, AutoIncResult),

    true = ets:insert_new(auto_increment, {{{{{0}, {1}}}, AutoIncStart}}),
", table.name, table.auto_increment);
                        }
                        else
                        {
                            game_db_init.AppendFormat(@"
    ?INFO(""game_db init: {0} start~n"", []),
	
", table.name);
                        }

                        if (table.IsWriteOnly)
                        {
                            game_db_init.AppendLine("    ok");
                        }
                        else
                        {
                            if (table.CanFrag)
                            {
                                game_db_init.AppendFormat(@"
    [ets:new(list_to_atom(""t_{0}_"" ++ integer_to_list(I)), [public, set, named_table, {{keypos, 2}}]) || I <- lists:seq(0, 99)],
    
    load({0}),
    ok", table.name);
                            }
                            else
                            {
                                game_db_init.AppendFormat(@"
    ets:new(t_{0}, [public, set, named_table, {{keypos, 2}}]),
    
    load({0}),
    ok", table.name);
                            }
                        }

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
                        if (table.IsWriteOnly)
                        {
                            n += 1;
                            continue;
                        }

                        game_db_init.AppendFormat(@"
load ({0}) ->    
    {{data, NumResultId}} = mysql:fetch(gamedb, [<<""select count(1) as num from `{0}`"">>]),
    {{num, RecordNumber}} = lists:keyfind(num, 1, hd(lib_mysql:get_rows(NumResultId))),

    lists:foreach(fun(Page) ->
        Sql = ""SELECT * FROM `{0}` LIMIT "" ++  integer_to_list((Page - 1) * 100000) ++ "", 100000"",
        {{data, ResultId}} = mysql:fetch(gamedb, [list_to_binary(Sql)]),

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

                        if (table.CanFrag)
                        {
                            game_db_init.AppendFormat(@"
            
           TabId  = integer_to_list((Record #{0}.{1}) rem 100),
                EtsTab = list_to_atom(""t_{0}_"" ++ TabId),

                ets:insert(EtsTab, Record)
            end,
            Rows
        ) end,
        lists:seq(1, lib_misc:ceil(RecordNumber / 100000))
    ),

    ?INFO(
        ""game_db init: {0} finished~n""
        ""===================================================================~n"", []
    ),
    ok", table.name, table.FragKey);
                        }
                        else
                        {
                            game_db_init.AppendFormat(@"
            ets:insert(t_{0}, Record)
        end,
        Rows
        ) end,
        lists:seq(1, lib_misc:ceil(RecordNumber / 100000))
    ),

    ?INFO(
        ""game_db init: {0} finished~n""
        ""===================================================================~n"", []
    ),
    ok", table.name);
                        }

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
    start_link/0,
    dirty_select/3, 
    dirty_select/2, 
    dirty_read/1, 
    select/3, 
    select/2, 
    read/1, 
    write/1, 
    delete/1, 
    delete_select/3, 
    delete_select/2, 
    table/1, 
    table/2,
    ets/1,
    ets/2,
    count/1,
    memory/0,
    memory/1,
    fetch/1,
    delete_all/1,
    do/1
]).

-include(""game.hrl"").
-include(""gen/game_db.hrl"").

%-ifdef(debug).

-define(ENSURE_TRAN, ensure_tran()).

ensure_tran() -> case get(tran_action_list) of undefined -> exit(need_gamedb_tran); _ -> ok end.

%-else.

%-define(ENSURE_TRAN, ok).

%-endif.

start_link() ->
    proc_lib:start_link(game_db_init, init, []).

dirty_select (Table, PlayerId, MatchSpec) ->
    select(Table, PlayerId, MatchSpec).

dirty_select (Table, MatchSpec) ->
    select(Table, MatchSpec).

dirty_read (Key) ->
    read(Key).

");

                    List<TableInfo2> fragTables = new List<TableInfo2>();
                    List<TableInfo2> normalTables = new List<TableInfo2>();

                    foreach (TableInfo2 table in tables)
                    {
                        if (table.CanFrag)
                        {
                            fragTables.Add(table);
                        }
                        else
                        {
                            normalTables.Add(table);
                        }
                    }

                    #region select/3
                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in fragTables)
                    {
                        code.Append("select (").Append(table.name).Append(", ModeOrFragId, MatchSpec) ->").AppendLine();

                        if (developMode)
                        {
                            code.Append("    {Time1, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time2, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    R =");
                        }

                        code.Append("    case ModeOrFragId of").AppendLine();
                        code.Append("        slow -> ").AppendLine();
                        code.Append("            fetch_select(\"t_").Append(table.name).Append("_\", MatchSpec);").AppendLine();
                        code.Append("        FragId ->").AppendLine();
                        code.Append("            ets:select(list_to_atom(\"t_").Append(table.name).Append("_\" ++ integer_to_list(FragId rem 100)), MatchSpec)");
                        code.Append("    end");

                        if (developMode)
                        {
                            code.Append(",").AppendLine();
                            code.Append("    {Time3, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time4, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    Sec1 = (Time3 - Time1) / 1000.0,").AppendLine();
                            code.Append("    Sec2 = (Time4 - Time2) / 1000.0,").AppendLine();
                            code.Append("    game_prof_srv:set_info(game_db, 'select.").Append(table.name).Append("', Sec1, Sec2),").AppendLine();
                            code.Append("    R");
                        }

                        if (n < fragTables.Count - 1)
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

                    foreach (TableInfo2 table in normalTables)
                    {
                        code.Append("select (").Append(table.name).Append(", MatchSpec) ->").AppendLine();

                        if (developMode)
                        {
                            code.Append("    {Time1, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time2, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    R =");
                        }

                        code.Append("    ets:select(t_").Append(table.name).Append(", MatchSpec)");

                        if (developMode)
                        {
                            code.Append(",").AppendLine();
                            code.Append("    {Time3, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time4, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    Sec1 = (Time3 - Time1)/ 1000.0,").AppendLine();
                            code.Append("    Sec2 = (Time4 - Time2)/ 1000.0,").AppendLine();
                            code.Append("    game_prof_srv:set_info(game_db, 'select.").Append(table.name).Append("', Sec1, Sec2),").AppendLine();
                            code.Append("    R");
                        }

                        if (n < normalTables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }
                    #endregion

                    #region delete_all/1
                    code.AppendLine();

                    n = 0;
                    int special_table_count = 14;

                    foreach (TableInfo2 table in tables)
                    {             
                        // special_table_count need update
                        if (table.name == "player_world_twin_dragons" ||
                            table.name == "player_world_twin_dragons_race" ||
                            table.name == "player_world_twin_dragons_report" ||
                            table.name == "player_world_twin_dragons_server" ||
                            table.name == "player_twin_dragons" ||
                            table.name == "player_twin_dragons_bet" ||
                            table.name == "player_twin_dragons_team_deploy" ||
                            table.name == "player_twin_dragons_team" ||
                            table.name == "player_twin_dragons_team_member" ||
                            table.name == "player_twin_dragons_team_apply" ||
                            table.name == "player_twin_dragons_report" ||
                            table.name == "player_twin_dragons_race" ||
                            table.name == "player_week_ranking_award_data" ||
                            table.name == "player_super_sport_lucky_ranking")
                        {
                            code.Append("delete_all (").Append(table.name).Append(") ->").AppendLine();

                            if (developMode)
                            {
                                code.Append("    {Time1, _} = statistics(runtime),").AppendLine();
                                code.Append("    {Time2, _} = statistics(wall_clock),").AppendLine();
                                code.Append("    R =");
                            }

                            if (normalTables.Contains(table))
                            {
                                code.Append("ets:delete_all_objects(t_").Append(table.name).Append("),");
                            }
                            else
                            {
                                code.Append("[ets:delete_all_objects(list_to_atom(\"t_").Append(table.name).Append("_\" ++ integer_to_list(Id))) || Id <- lists:seq(0, 99)],");
                            }
                            code.Append("\n    add_tran_action({").Append(table.name).Append(", sql, \"DELETE FROM `").Append(table.name).Append("`;\"})");

                            if (developMode)
                            {
                                code.Append(",").AppendLine();
                                code.Append("    {Time3, _} = statistics(runtime),").AppendLine();
                                code.Append("    {Time4, _} = statistics(wall_clock),").AppendLine();
                                code.Append("    Sec1 = (Time3 - Time1) / 1000.0,").AppendLine();
                                code.Append("    Sec2 = (Time4 - Time2) / 1000.0,").AppendLine();
                                code.Append("    game_prof_srv:set_info(game_db, 'delete_all.").Append(table.name).Append("', Sec1, Sec2),").AppendLine();
                                code.Append("    R");
                            }

                            if (n < special_table_count - 1)
                                code.AppendLine(";");
                            else
                                code.AppendLine(".");

                            code.AppendLine();

                            n += 1;
                        }   
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

                        if (developMode)
                        {
                            code.Append("    {Time1, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time2, _} = statistics(wall_clock),").AppendLine();

                            code.Append("    R =");
                        }

                        if (table.CanFrag)
                        {
                            if (table.primaryKeys.Contains(table.FragKey))
                            {
                                code.Append("    ets:lookup(list_to_atom(\"t_").Append(table.name).Append("_\" ++ integer_to_list(").Append(FormatName(table.FragKey)).Append(" rem 100)), {");
                            }
                            else
                            {
                                code.Append("    fetch_lookup(\"t_").Append(table.name).Append("_\", {");
                            }
                        }
                        else
                        {
                            code.Append("    ets:lookup(t_").Append(table.name).Append(", {");
                        }

                        for (int i = 0; i < table.primaryKeys.Count; i++)
                        {
                            code.Append(FormatName(table.primaryKeys[i]));

                            if (i < table.primaryKeys.Count - 1)
                                code.Append(", ");
                        }

                        code.Append("})");

                        if (developMode)
                        {
                            code.AppendLine(",");
                            code.Append("    {Time3, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time4, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    Sec1 = (Time3 - Time1)/ 1000.0,").AppendLine();
                            code.Append("    Sec2 = (Time4 - Time2)/ 1000.0,").AppendLine();
                            code.Append("    game_prof_srv:set_info(game_db, 'read.").Append(table.name).Append("', Sec1, Sec2),").AppendLine();
                            code.Append("    R");
                        }

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

                        if (developMode)
                        {
                            code.Append("    {Time1, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time2, _} = statistics(wall_clock),").AppendLine();
                        }

                        if (table.CanFrag)
                        {
                            code.Append("    EtsTable = list_to_atom(\"t_").Append(table.name).Append("_\" ++ integer_to_list(Record #").Append(table.name).Append(".").Append(table.FragKey).Append(" rem 100)),").AppendLine();
                        }
                        else
                        {
                            code.Append("    EtsTable = t_").Append(table.name).Append(",").AppendLine();
                        }

                        if (developMode)
                        {
                            code.Append("    R =");
                        }

                        code.Append("    case Record #").Append(table.name).Append(".row_key of").AppendLine();
                        code.Append("        undefined ->").AppendLine();

                        #region insert

                        if (table.auto_increment != string.Empty)
                        {
                            code.Append("            validate_for_insert(Record),").AppendLine().AppendLine();
                            code.Append("            NewId = ets:update_counter(auto_increment, {").Append(table.name).Append(", ").Append(table.auto_increment).Append("}, 1),").AppendLine().AppendLine();

                            if (table.name == "player")
                            {
                                code.Append("            put(the_player_id, NewId),").AppendLine();
                            }

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

                            if (table.IsWriteOnly == false)
                            {
                                code.Append("            true = ets:insert_new(EtsTable, NewRecord),").AppendLine().AppendLine();

                                code.Append("            add_tran_log({insert, EtsTable, NewRecord #").Append(table.name).Append(".row_key}),").AppendLine().AppendLine();

                                code.Append("            add_tran_action({").Append(table.name).Append(", insert, NewRecord}),").AppendLine().AppendLine();
                            }
                            else
                            {
                                code.Append("            add_tran_action({").Append(table.name).Append(", insert, NewRecord}),").AppendLine().AppendLine();
                            }

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

                            if (table.IsWriteOnly == false)
                            {
                                code.Append("            true = ets:insert_new(EtsTable, NewRecord),").AppendLine().AppendLine();

                                code.Append("            add_tran_log({insert, EtsTable, NewRecord #").Append(table.name).Append(".row_key}),").AppendLine().AppendLine();
                            }

                            code.Append("            add_tran_action({").Append(table.name).Append(", insert, NewRecord}),").AppendLine().AppendLine();
                            /*
                            if (table.name == "player_deploy_grid")
                            {
                                code.Append("            game_cache_srv:player_change(Record #player_deploy_grid.player_id, [{?CHD_PLAYER_DEPLOY_GRID, Record #player_deploy_grid.deploy_mode_id}]),").AppendLine();
                            }
                            else if (table.name == "player_quest" || table.name == "player_quest_monster" || table.name == "player_item")
                            {
                                code.Append("            game_cache_srv:player_change(Record #").Append(table.name).Append(".player_id, [?CHD_PLAYER_QUEST]),").AppendLine();
                            }
                            */
                            code.Append("            {ok, NewRecord};").AppendLine();
                        }

                        #endregion

                        code.AppendLine();

                        code.Append("        _ ->").AppendLine();

                        #region update

                        code.Append("            validate_for_update(Record),").AppendLine().AppendLine();

                        code.Append("            [OldRecord] = ets:lookup(EtsTable, Record #").Append(table.name).Append(".row_key),").AppendLine().AppendLine();

                        if (developMode)
                            code.Append("            if OldRecord #").Append(table.name).Append(".row_ver =:= Record #").Append(table.name).Append(".row_ver -> ok end,").AppendLine().AppendLine();

                        if (table.name == "player_data")
                        {
                            code.Append(@"
            if OldRecord #player_data.ingot =/= Record #player_data.ingot ->
                case get(ingot_op_reason) of
                    undefined -> exit(unknow_ingot_op_reason);
                    _ -> ok
                end;
            true -> 
                ok
            end,
");
                        }

                        code.Append("            Changes = get_changes(").Append(table.columns.Count + 2).Append(", Record, OldRecord),").AppendLine().AppendLine();

                        if (developMode)
                            code.Append("            RealNewRecord = Record #").Append(table.name).Append("{ row_ver = Record #").Append(table.name).Append(".row_ver + 1},").AppendLine().AppendLine();
                        else
                            code.Append("            RealNewRecord = Record,").AppendLine().AppendLine();

                        code.Append("            ets:insert(EtsTable, RealNewRecord),").AppendLine();

                        code.Append("            add_tran_log({update, EtsTable, OldRecord}),").AppendLine().AppendLine();

                        code.Append("            add_tran_action({").Append(table.name).Append(", update, Record, Changes}),").AppendLine().AppendLine();
                        /*
                        if (table.name == "player")
                        {
                            code.Append("            game_cache_srv:player_change(Record #player.id, [?CHD_PLAYER]),").AppendLine();
                            code.Append(@"
            RecordPlayerId = Record #player.id,
            case get(the_player_id) of
            	RecordPlayerId ->
            		game_cache_srv:player_set(get_player, [?CHD_PLAYER], RealNewRecord);
                _ ->
                    ok
            end,").AppendLine();
                        }
                        else if (table.name == "player_data")
                        {
                            code.Append("            game_cache_srv:player_change(Record #player_data.player_id, [?CHD_PLAYER_DATA]),").AppendLine();
                            code.Append(@"
            RecordPlayerId = Record #player_data.player_id,
            case get(the_player_id) of
            	RecordPlayerId ->
            		game_cache_srv:player_set(get_player_data, [?CHD_PLAYER_DATA], RealNewRecord);
                _ ->
                    ok
            end,").AppendLine();
                        }
                        else if (table.name == "player_key")
                        {
                            code.Append("            game_cache_srv:player_change(Record #player_key.player_id, [?CHD_PLAYER_KEY]),").AppendLine();
                            code.Append(@"
            RecordPlayerId = Record #player_key.player_id,
            case get(the_player_id) of
            	RecordPlayerId ->
            		game_cache_srv:player_set(get_player_key, [?CHD_PLAYER_KEY], RealNewRecord);
                _ ->
                    ok
            end,").AppendLine();
                        }
                        else if (table.name == "player_role")
                        {
                            code.Append("            game_cache_srv:player_change(Record #player_role.player_id, [{?CHD_PLAYER_ROLE, Record #player_role.id}]),").AppendLine();
                            code.Append(@"
            RecordPlayerId = Record #player_role.player_id,
            case get(the_player_id) of
            	RecordPlayerId ->
            		game_cache_srv:player_set({get_player_role, Record #player_role.id}, [{?CHD_PLAYER_ROLE, Record #player_role.id}], RealNewRecord);
                _ ->
                    ok
            end,").AppendLine();
                        }
                        else if (table.name == "player_role_data")
                        {
                            code.Append("            game_cache_srv:player_change(Record #player_role_data.player_id, [{?CHD_PLAYER_ROLE_DATA, Record #player_role_data.player_role_id}]),").AppendLine();
                            code.Append(@"
            RecordPlayerId = Record #player_role_data.player_id,
            case get(the_player_id) of
            	RecordPlayerId ->
            		game_cache_srv:player_set({get_player_role_data, Record #player_role_data.player_role_id}, [{?CHD_PLAYER_ROLE_DATA, Record #player_role_data.player_role_id}], RealNewRecord);
                _ ->
                    ok
            end,").AppendLine();
                        }
                        else if (table.name == "player_deploy_grid")
                        {
                            code.Append("            game_cache_srv:player_change(Record #player_deploy_grid.player_id, [{?CHD_PLAYER_DEPLOY_GRID, Record #player_deploy_grid.deploy_mode_id}]),").AppendLine();
                        }
                        else if (table.name == "player_quest" || table.name == "player_quest_monster" || table.name == "player_item")
                        {
                            code.Append("            game_cache_srv:player_change(Record #").Append(table.name).Append(".player_id, [?CHD_PLAYER_QUEST]),").AppendLine();
                        }
                        else if (table.name == "player_fate")
                        {
                            code.Append("            game_cache_srv:player_change(Record #player_fate.player_id, [{?CHD_PLAYER_FATE, Record #player_fate.player_role_id}]),").AppendLine();
                            code.Append("            game_cache_srv:player_change(OldRecord #player_fate.player_id, [{?CHD_PLAYER_FATE, OldRecord #player_fate.player_role_id}]),").AppendLine();
                        }
                        */

                        code.Append("            {ok, RealNewRecord}").AppendLine();

                        #endregion


                        code.Append("    end");

                        if (developMode)
                        {
                            code.AppendLine(",");
                            code.Append("    {Time3, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time4, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    Sec1 = (Time3 - Time1)/ 1000.0,").AppendLine();
                            code.Append("    Sec2 = (Time4 - Time2)/ 1000.0,").AppendLine();
                            code.Append("    game_prof_srv:set_info(game_db, 'write.").Append(table.name).Append("', Sec1, Sec2),").AppendLine();
                            code.Append("    R");
                        }

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

                        if (developMode)
                        {
                            code.Append("    {Time1, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time2, _} = statistics(wall_clock),").AppendLine();
                        }

                        if (table.CanFrag)
                        {
                            code.Append("    EtsTable = list_to_atom(\"t_").Append(table.name).Append("_\" ++ integer_to_list(Record #").Append(table.name).Append(".").Append(table.FragKey).Append(" rem 100)),").AppendLine();
                        }
                        else
                        {
                            code.Append("    EtsTable = t_").Append(table.name).Append(",").AppendLine();
                        }

                        code.Append("    ets:delete(EtsTable, RowKey),").AppendLine();

                        code.Append("    add_tran_log({delete, EtsTable, Record}),").AppendLine().AppendLine();

                        code.Append("    add_tran_action({").Append(table.name).Append(", delete, Record}),").AppendLine().AppendLine();

                        if (developMode)
                        {
                            code.Append("    {Time3, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time4, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    Sec1 = (Time3 - Time1)/ 1000.0,").AppendLine();
                            code.Append("    Sec2 = (Time4 - Time2)/ 1000.0,").AppendLine();
                            code.Append("    game_prof_srv:set_info(game_db, 'delete.").Append(table.name).Append("', Sec1, Sec2),").AppendLine();
                        }
                        /*
                        if (table.name == "player_deploy_grid")
                        {
                            code.Append("            game_cache_srv:player_change(Record #player_deploy_grid.player_id, [{?CHD_PLAYER_DEPLOY_GRID, Record #player_deploy_grid.deploy_mode_id}]),").AppendLine();
                        }
                        else if (table.name == "player_quest" || table.name == "player_quest_monster" || table.name == "player_item")
                        {
                            code.Append("            game_cache_srv:player_change(Record #").Append(table.name).Append(".player_id, [?CHD_PLAYER_QUEST]),").AppendLine();
                        }
                        */
                        code.Append("    ok");

                        if (n < tables.Count - 1)
                            code.AppendLine(";");
                        else
                            code.AppendLine(".");

                        code.AppendLine();

                        n += 1;
                    }

                    #endregion

                    #region delete_select/3
                    code.AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in fragTables)
                    {
                        code.Append("delete_select (").Append(table.name).Append(", ModeOrFragKey, MatchSpec) -> ?ENSURE_TRAN,").AppendLine();

                        if (developMode)
                        {
                            code.Append("    {Time1, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time2, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    R =");
                        }

                        code.Append("    case select(").Append(table.name).Append(", ModeOrFragKey, MatchSpec) of").AppendLine();
                        code.Append("        [] -> {ok, 0};").AppendLine();
                        code.Append("        Rows when is_list(Rows) ->").AppendLine();
                        code.Append("            Num = lists:foldl(fun(Row, Count) ->").AppendLine();
                        code.Append("                delete(Row),").AppendLine();
                        code.Append("                Count + 1").AppendLine();
                        code.Append("            end, 0, Rows),").AppendLine();
                        code.Append("            {ok, Num}").AppendLine();
                        code.Append("    end");

                        if (developMode)
                        {
                            code.AppendLine(",");
                            code.Append("    {Time3, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time4, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    Sec1 = (Time3 - Time1)/ 1000.0,").AppendLine();
                            code.Append("    Sec2 = (Time4 - Time2)/ 1000.0,").AppendLine();
                            code.Append("    game_prof_srv:set_info(game_db, 'delete_select.").Append(table.name).Append("', Sec1, Sec2),").AppendLine();
                            code.Append("    R");
                        }

                        if (n < fragTables.Count - 1)
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

                    foreach (TableInfo2 table in normalTables)
                    {
                        code.Append("delete_select (").Append(table.name).Append(", MatchSpec) -> ?ENSURE_TRAN,").AppendLine();

                        if (developMode)
                        {
                            code.Append("    {Time1, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time2, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    R =");
                        }

                        code.Append("    case select(").Append(table.name).Append(", MatchSpec) of").AppendLine();
                        code.Append("        [] -> {ok, 0};").AppendLine();
                        code.Append("        Rows when is_list(Rows) ->").AppendLine();
                        code.Append("            Num = lists:foldl(fun(Row, Count) ->").AppendLine();
                        code.Append("                delete(Row),").AppendLine();
                        code.Append("                Count + 1").AppendLine();
                        code.Append("            end, 0, Rows),").AppendLine();
                        code.Append("            {ok, Num}").AppendLine();
                        code.Append("    end");

                        if (developMode)
                        {
                            code.AppendLine(",");
                            code.Append("    {Time3, _} = statistics(runtime),").AppendLine();
                            code.Append("    {Time4, _} = statistics(wall_clock),").AppendLine();
                            code.Append("    Sec1 = (Time3 - Time1)/ 1000.0,").AppendLine();
                            code.Append("    Sec2 = (Time4 - Time2)/ 1000.0,").AppendLine();
                            code.Append("    game_prof_srv:set_info(game_db, 'delete_select.").Append(table.name).Append("', Sec1, Sec2),").AppendLine();
                            code.Append("    R");
                        }

                        if (n < normalTables.Count - 1)
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
                        code.Append("table (").Append(table.name).Append(") -> ets:table(t_").Append(table.name).Append(")");

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
                        code.Append("table (").Append(table.name).Append(", Options) -> ets:table(").Append(table.name).Append(", Options)");

                        if (n == tables.Count - 1)
                            code.AppendLine(".").AppendLine().AppendLine();
                        else
                            code.AppendLine(";");

                        n += 1;
                    }

                    #endregion

                    #region ets/1

                    n = 0;

                    foreach (TableInfo2 table in normalTables)
                    {
                        code.Append("ets (").Append(table.name).Append(") -> t_").Append(table.name).Append("");

                        if (n == normalTables.Count - 1)
                            code.AppendLine(".").AppendLine().AppendLine();
                        else
                            code.AppendLine(";");

                        n += 1;
                    }

                    #endregion

                    #region ets/2

                    n = 0;

                    foreach (TableInfo2 table in fragTables)
                    {
                        code.Append("ets (").Append(table.name).Append(", FragId) -> list_to_atom(\"t_").Append(table.name).Append("_\" ++ integer_to_list(FragId rem 100))");

                        if (n == fragTables.Count - 1)
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

                    #region count/1

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("count (").Append(table.name).Append(") -> {size, Size} =lists:keyfind(size, 1, ets:info(t_").Append(table.name).Append(")), Size");

                        if (n == tables.Count - 1)
                            code.AppendLine(".").AppendLine().AppendLine();
                        else
                            code.AppendLine(";");

                        n += 1;
                    }

                    #endregion

                    #region memory/1

                    code.Append("memory () ->").AppendLine();

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("    memory(").Append(table.name).Append(")");

                        if (n == tables.Count - 1)
                            code.AppendLine(".").AppendLine().AppendLine();
                        else
                            code.AppendLine(" +");

                        n += 1;
                    }

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        code.Append("memory (").Append(table.name).Append(") -> {memory, Memory} =lists:keyfind(memory, 1, ets:info(t_").Append(table.name).Append(")), Memory");

                        if (n == tables.Count - 1)
                            code.AppendLine(".").AppendLine().AppendLine();
                        else
                            code.AppendLine(";");

                        n += 1;
                    }
                    #endregion

                    #region fetch

                    code.Append("fetch (Sql) ->\n    {data, ResultId} = mysql:fetch(gamedb, Sql),\n");
                    code.Append("    lib_mysql:get_rows(ResultId).");

                    #endregion

                    code.Append(
@"

do (Tran) ->
	case get(tran_action_list) of
        undefined ->
            put(tran_log, []),
            put(tran_action_list, []),
            put(tran_action_list2, []),

            case catch Tran() of
                {'EXIT', {aborted, Reason}} -> 
                    rollback(get(tran_log)),
                    erase(tran_log),
                    erase(tran_action_list),
                    erase(tran_action_list2),
                    exit(Reason);

                {'EXIT', Reason} -> 
                    rollback(get(tran_log)),
                    erase(tran_log),
                    erase(tran_action_list),
                    erase(tran_action_list2),
                    exit(Reason);
                    
                Result ->
                    erase(tran_log),
                    TranActionList = erase(tran_action_list),

                    case TranActionList of
                        [] -> ok;
                         _ -> game_db_sync_proc ! {sync, TranActionList}
                    end,

                    {atomic, Result}
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
            ets:delete(Table, RowKey);
        {update, Table, Row} ->
            %if
            %    is_record(Row, player)           -> game_cache_srv:player_change(Row #player.id, [?CHD_PLAYER]);
            %    is_record(Row, player_data)      -> game_cache_srv:player_change(Row #player_data.player_id, [?CHD_PLAYER_DATA]);
            %    is_record(Row, player_key)       -> game_cache_srv:player_change(Row #player_key.player_id, [?CHD_PLAYER_KEY]);
            %    is_record(Row, player_role)      -> game_cache_srv:player_change(Row #player_role.player_id, [{?CHD_PLAYER_ROLE, Row #player_role.id}]);
            %    is_record(Row, player_role_data) -> game_cache_srv:player_change(Row #player_role_data.player_id, [{?CHD_PLAYER_ROLE_DATA, Row #player_role_data.player_role_id}]);
            %    true -> ok
            %end,
            ets:insert(Table, Row);
        {delete, Table, Row} ->
            ets:insert(Table, Row)
    end,
    rollback(Term).

get_changes(N, NewRecord, OldRecord) ->
    get_changes(N, NewRecord, OldRecord, []).
    
get_changes(2, _, _, Changes) -> 
    Changes;
get_changes(N, NewRecord, OldRecord, Changes) ->
    case element(N, NewRecord) =:= element(N, OldRecord) of
        true -> get_changes(N - 1, NewRecord, OldRecord, Changes);
        false -> get_changes(N - 1, NewRecord, OldRecord, [N | Changes])
    end.

fetch_lookup(TablePrefix, Key) ->
    fetch_lookup(TablePrefix, Key, 0).

fetch_lookup(_, _, 100) ->
    [];
fetch_lookup(TablePrefix, Key, N) ->
    case ets:lookup(list_to_atom(TablePrefix ++ integer_to_list(N)), Key) of
        [] -> fetch_lookup(TablePrefix, Key, N + 1);
        R  -> R
    end.

fetch_select(TablePrefix, MatchSpec) ->
    fetch_select(TablePrefix, MatchSpec, 0, []).

fetch_select(_, _, 100, Result) ->
    lists:concat(Result);
fetch_select(TablePrefix, MatchSpec, N, Result) ->
    fetch_select(TablePrefix, MatchSpec, N + 1, [
        ets:select(list_to_atom(TablePrefix ++ integer_to_list(N)), MatchSpec) | Result
    ]).
");

                    using (StreamWriter writer = new StreamWriter(codeFile, false))
                    {
                        writer.Write(code.ToString());
                    }

                    #endregion

                    #region game_db_sync

                    StringBuilder game_db_sync = new StringBuilder();

                    game_db_sync.Append(@"-module(game_db_sync).

-export([
    start_proc/0,   
    sync_proc_init/0,

    start_worker0/0,
    start_worker1/0,

    sync_worker0_init/0, 
    sync_worker1_init/0,

    wait_for_all_data_sync0/1, 
    wait_for_all_data_sync1/1,

    count_work0/0,
    count_work1/0
]).

-include(""game.hrl"").
-include(""gen/game_db.hrl"").

");
                    #region tran_action_to_sql/1

                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        int n2 = 0;

                        #region sql

                        game_db_sync.Append("tran_action_to_sql ({").Append(table.name).Append(", sql, Sql}) ->").AppendLine();

                        game_db_sync.Append("    list_to_binary(Sql);");

                        game_db_sync.AppendLine();

                        #endregion

                        #region insert

                        game_db_sync.Append("tran_action_to_sql ({").Append(table.name).Append(", insert, Record}) ->").AppendLine();

                        foreach (string column in table.columns)
                        {
                            game_db_sync.Append("    ");

                            string space = GenerateSpace(table.maxlength, FormatName(column).Length);

                            if (TableInfo2.IsIntegerType(table.types[column]))
                                game_db_sync.AppendFormat("{0}{3}= int_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);
                            else if (TableInfo2.IsFloatType(table.types[column]))
                                game_db_sync.AppendFormat("{0}{3}= rel_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);
                            else
                                game_db_sync.AppendFormat("{0}{3}= lst_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);

                            game_db_sync.AppendLine();
                        }

                        game_db_sync.AppendLine();

                        game_db_sync.AppendFormat(@"    <<
    ""INSERT IGNORE INTO `{0}` SET """, table.name);

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

                        game_db_sync.AppendFormat(@""";\n""
    >>;");

                        game_db_sync.AppendLine();
                        game_db_sync.AppendLine();

                        #endregion

                        #region delete

                        game_db_sync.Append("tran_action_to_sql ({").Append(table.name).Append(", delete, Record}) ->").AppendLine();

                        foreach (string primaryKey in table.primaryKeys)
                        {
                            game_db_sync.Append("    ");

                            if (TableInfo2.IsIntegerType(table.types[primaryKey]))
                                game_db_sync.AppendFormat("{0} = int_to_bin(Record #{1}.{2}),", FormatName(primaryKey), table.name, primaryKey);
                            else
                                game_db_sync.AppendFormat("{0} = lst_to_bin(Record #{1}.{2}),", FormatName(primaryKey), table.name, primaryKey);

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

                            game_db_sync.AppendFormat("\" `{0}` = \", {1}/binary, ", primaryKey, FormatName(primaryKey));

                            if (n2 < table.primaryKeys.Count - 1)
                                game_db_sync.Append("\" AND \",");

                            n2 += 1;
                        }

                        game_db_sync.AppendLine(@""";\n""
    >>;");

                        game_db_sync.AppendLine();
                        game_db_sync.AppendLine();

                        #endregion

                        #region update

                        game_db_sync.Append("tran_action_to_sql ({").Append(table.name).Append(", update, _, []}) ->").AppendLine();
                        game_db_sync.Append("    none;").AppendLine();
                        game_db_sync.Append("tran_action_to_sql ({").Append(table.name).Append(", update, Record, Changes}) ->").AppendLine();
                        game_db_sync.Append("    Sql = generate_update_sql(").Append(table.name).Append(", Record, Changes, [<<\"UPDATE `").Append(table.name).Append("` SET \">>]),").AppendLine();
                        game_db_sync.Append("    list_to_binary(lists:reverse(Sql))");

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

                    #region generate_sql
                    n = 0;

                    foreach (TableInfo2 table in tables)
                    {
                        int n2 = 0;

                        game_db_sync.Append("generate_update_sql (").Append(table.name).Append(", Record, [], Sql) ->").AppendLine();

                        foreach (string column in table.primaryKeys)
                        {
                            game_db_sync.Append("    ");

                            string space = GenerateSpace(table.maxlength, FormatName(column).Length);

                            if (TableInfo2.IsIntegerType(table.types[column]))
                                game_db_sync.AppendFormat("{0}{3}= int_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);
                            else if (TableInfo2.IsFloatType(table.types[column]))
                                game_db_sync.AppendFormat("{0}{3}= rel_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);
                            else
                                game_db_sync.AppendFormat("{0}{3}= lst_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);

                            game_db_sync.AppendLine();
                        }

                        game_db_sync.AppendFormat("    [<<\" WHERE \",");

                        n2 = 0;

                        foreach (string primaryKey in table.primaryKeys)
                        {
                            game_db_sync.AppendFormat(" \" `{0}` = \", {1}/binary,", primaryKey, FormatName(primaryKey));

                            if (n2 < table.primaryKeys.Count - 1)
                                game_db_sync.Append(" \" AND \",");

                            n2 += 1;
                        }

                        game_db_sync.Append(@" "";\n"">> | Sql];").AppendLine();

                        n2 = 1;

                        foreach (string column in table.columns)
                        {
                            if (column == table.auto_increment)
                            {
                                n2 += 1;
                                continue;
                            }

                            string binMethod = "";

                            if (TableInfo2.IsIntegerType(table.types[column]))
                                binMethod = "int_to_bin";
                            else if (TableInfo2.IsFloatType(table.types[column]))
                                binMethod = "rel_to_bin";
                            else
                                binMethod = "lst_to_bin";

                            game_db_sync.AppendLine();
                            game_db_sync.AppendFormat(@"
generate_update_sql ({0}, Record, [{3} | Changes], Sql) ->
    {1} = {4}(Record #{0}.{2}),
    Sql2 = case length(Sql) of 1 -> [<<""`{2}` = "", {1}/binary>> | Sql]; _ -> [<<"",`{2}` = "", {1}/binary>> | Sql] end,
    generate_update_sql ({0}, Record, Changes, Sql2)",
                            table.name,
                            FormatName(column),
                            column, n2 + 2,
                            binMethod);

                            if (n2 - 1 < table.columns.Count - 1)
                                game_db_sync.AppendLine(";");

                            n2 += 1;
                        }


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
count_work0 () ->
	{message_queue_len, Len} = process_info(whereis(game_db_sync_worker0), message_queue_len),
    Len.

count_work1 () ->
	{message_queue_len, Len} = process_info(whereis(game_db_sync_worker1), message_queue_len),
    Len.


wait_for_all_data_sync0 (TimeOut) ->
	wait_for_all_data_sync0(TimeOut, 0).
	
wait_for_all_data_sync0 (TimeOut, TimeOut) ->
    case io:get_chars(""Time out, continue? [Y/n] : "", 1) of
        ""n"" ->
	        io:format(""wait for all player data sync (0) ... time out""),
	        time_out;
        _ ->
	        wait_for_all_data_sync0(TimeOut, 0)
    end;
wait_for_all_data_sync0 (TimeOut, Time) ->
	io:format(""wait for all player data sync (0) ... ""),
	receive
	after 1000 ->
        case count_work0() of
            0 -> io:format(""done~n""), ok;
			N -> io:format(""~p~n"", [N]), wait_for_all_data_sync0(TimeOut, Time + 1)
		end
	end.

wait_for_all_data_sync1 (TimeOut) ->
	wait_for_all_data_sync1(TimeOut, 0).
	
wait_for_all_data_sync1 (TimeOut, TimeOut) ->
    case io:get_chars(""Time out, continue? [Y/n] : "", 1) of
        ""n"" ->
	        io:format(""wait for all player data sync (1) ... time out""),
	        time_out;
        _ ->
	        wait_for_all_data_sync1(TimeOut, 0)
    end;
wait_for_all_data_sync1 (TimeOut, Time) ->
	io:format(""wait for all player data sync (1) ... ""),
	receive
	after 1000 ->
        case count_work1() of
            0 -> io:format(""done~n""), ok;
			N -> io:format(""~p~n"", [N]), wait_for_all_data_sync1(TimeOut, Time + 1)
		end
	end.

start_proc () ->
    proc_lib:start_link(?MODULE, sync_proc_init, []).

sync_proc_init () ->
    register(game_db_sync_proc, self()),
    proc_lib:init_ack({ok, self()}),
    sync_proc_loop().

sync_proc_loop() ->
    receive
        {sync, TranActionList} ->
            case catch tran_action_list_to_sql_list(TranActionList) of
                [] -> 
                    sync_proc_loop();

                SqlList when is_list(SqlList) ->
                    game_db_sync_worker0 ! {work, SqlList},
                    game_db_sync_worker1 ! {work, SqlList},
                    sync_proc_loop();

                Error -> 
                    ?ERROR(""sync_proc_loop:  TranActionList = ~p~n  Error = ~p~n"", [TranActionList, Error]),
                    sync_proc_loop()
            end;
            
        {apply, From, M, F, A} ->
            From ! (catch apply(M, F, A)),
            sync_proc_loop();

        _ ->
            sync_proc_loop()
    end.


start_worker0 () ->
    proc_lib:start_link(?MODULE, sync_worker0_init, []).
    
sync_worker0_init () ->
    register(game_db_sync_worker0, self()),
    proc_lib:init_ack({ok, self()}),
    {{Y, M, D}, {H, MM,SS}} = erlang:localtime(),
    Time = {Y, M, D, H},
	{ok, LogFile} = get_log_file(Time),
	erlang:send_after((3600 - (MM * 60 + SS)) * 1000, self(), change_file),
    sync_worker0_loop(Time, LogFile).

sync_worker0_loop (Time, LogFile) ->
    receive
        {work, SqlList} ->
            case catch file:write(LogFile, [<<""\n"">> | SqlList]) of
                ok -> ok;
                Result -> ?ERROR(""sync_worker0_loop:  Result = ~p~n"", [Result])
            end,
            sync_worker0_loop(Time, LogFile);

		change_file ->
            ok = file:close(LogFile), 
			{{Y, M, D}, {H, MM, SS}} = erlang:localtime(),
			Time2 = {Y, M, D, H},
			{ok, LogFile2} = get_log_file(Time2),
			erlang:send_after((3600 - (MM * 60 + SS)) * 1000, self(), change_file),
			sync_worker0_loop(Time2, LogFile2);

        {apply, From, M, F, A} ->
            From ! (catch apply(M, F, A)),
            sync_worker0_loop(Time, LogFile);

        _ ->
            sync_worker0_loop(Time, LogFile)
    end.

start_worker1() ->
    proc_lib:start_link(?MODULE, sync_worker1_init, []).

sync_worker1_init () ->
    register(game_db_sync_worker1, self()),
    proc_lib:init_ack({ok, self()}),
    sync_worker1_loop().

sync_worker1_loop () ->
    receive
        {work, SqlList} ->
            case catch sync_sql_list1(SqlList) of
                {ok, _} -> ok;
                Result -> ?ERROR(""sync_worker1_loop:  SqlList = ~p~n  Result = ~p~n"", [SqlList, Result])
            end,
            sync_worker1_loop();

        {apply, From, M, F, A} ->
            From ! (catch apply(M, F, A)),
            sync_worker1_loop();

        _ ->
            sync_worker1_loop()
    end.

sync_sql_list1 (SqlList) ->
    {ok, mysql:fetch(gamedb, [SqlList], infinity)}.

tran_action_list_to_sql_list (TranActions) ->
    tran_action_list_to_sql_list(TranActions, []).
    
tran_action_list_to_sql_list ([], SqlList) ->
    SqlList;
tran_action_list_to_sql_list ([TranAction | Tail], SqlList) ->
    case tran_action_to_sql(TranAction) of
        none -> tran_action_list_to_sql_list(Tail, SqlList);
        Sql  -> tran_action_list_to_sql_list(Tail, [Sql | SqlList])
    end.
    
    
lst_to_bin (null) ->
	<<""NULL"">>;
lst_to_bin (List) ->
	List2 = escape_str(List, []),
	Bin = list_to_binary(List2),
	<<""'"", Bin/binary, ""'"">>.
	
int_to_bin (null) ->
    <<""NULL"">>;
int_to_bin (Value) ->
    list_to_binary(integer_to_list(Value)).

rel_to_bin (null) ->
    <<""NULL"">>;
rel_to_bin (Value) when is_integer(Value) ->
    list_to_binary(integer_to_list(Value));
rel_to_bin (Value) ->
    list_to_binary(float_to_list(Value)).

escape_str ([], Result) ->
	lists:reverse(Result);
escape_str ([$' | String], Result) ->
	escape_str(String, [$' | [$\\ | Result]]);
escape_str ([$"" | String], Result) ->
	escape_str(String, [$"" | [$\\ | Result]]);
escape_str ([$\\ | String], Result) ->
	escape_str(String, [$\\ | [$\\ | Result]]);
escape_str ([Char | String], Result) ->
	escape_str(String, [Char | Result]).

get_log_file({Y, M, D, H}) ->
    FileName = ""data/"" ++ integer_to_list(Y) ++ ""_"" ++ integer_to_list(M) ++ ""_"" ++ integer_to_list(D) ++ ""/"" ++ integer_to_list(H) ++ "".sql"",
    case filelib:is_file(FileName) of
        true -> ok;
        false -> ok = filelib:ensure_dir(FileName)
    end,
	{ok, File} = file:open(FileName, [append, raw, {delayed_write, 1024, 1000}]),
    ok = file:write(File, <<""/*!40101 SET NAMES utf8 */;\n"">>),
    ok = file:write(File, <<""/*!40101 SET SQL_MODE=''*/;\n"">>),
    ok = file:write(File, <<""/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n"">>),
    ok = file:write(File, <<""/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n"">>),
    ok = file:write(File, <<""/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n"">>),
    ok = file:write(File, <<""/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n"">>),
    {ok, File}.
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

                    #region game_db_dump
                    {
                        StringBuilder game_db_dump = new StringBuilder();

                        game_db_dump.Append(@"-module(game_db_dump).
 -export([run/0, backup/0]).

-include(""game.hrl"").
-include(""gen/game_db.hrl"").

run()->
    supervisor:terminate_child(game, socket_server_sup),

    mod_online:wait_all_online_player_exit(15),

    FileName = ""./game_db.sql"",

    case filelib:is_file(FileName) of
        true -> ok;
        false -> ok = filelib:ensure_dir(FileName)
    end,

    {ok, File} = file:open(FileName, [write, raw]),

    ok = file:write(File, <<""/*!40101 SET NAMES utf8 */;\n"">>),
    ok = file:write(File, <<""/*!40101 SET SQL_MODE=''*/;\n"">>),
    ok = file:write(File, <<""/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n"">>),
    ok = file:write(File, <<""/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n"">>),
    ok = file:write(File, <<""/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n"">>),
    ok = file:write(File, <<""/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n"">>),

");
                        foreach (TableInfo2 table in tables)
                        {
                            if (table.IsWriteOnly)
                                continue;

                            game_db_dump.Append("    dump_").Append(table.name).AppendLine("(File),");
                        }

                        game_db_dump.AppendLine("    ok = file:close(File),");
                        game_db_dump.AppendLine("    ok.");
                        game_db_dump.AppendLine();

                        game_db_dump.Append(@"

backup ()->
    FileName = ""./game_db_backup.sql"",

    case filelib:is_file(FileName) of
        true -> ok;
        false -> ok = filelib:ensure_dir(FileName)
    end,

    {ok, File} = file:open(FileName, [write, raw]),

    ok = file:write(File, <<""/*!40101 SET NAMES utf8 */;\n"">>),
    ok = file:write(File, <<""/*!40101 SET SQL_MODE=''*/;\n"">>),
    ok = file:write(File, <<""/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n"">>),
    ok = file:write(File, <<""/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n"">>),
    ok = file:write(File, <<""/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n"">>),
    ok = file:write(File, <<""/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n"">>),

                        ");
                        foreach (TableInfo2 table in tables)
                        {
                            if (table.IsWriteOnly)
                                continue;

                            game_db_dump.Append("    dump_").Append(table.name).AppendLine("(File),");
                        }

                        game_db_dump.AppendLine("    ok = file:close(File),");
                        game_db_dump.AppendLine("    ok.");
                        game_db_dump.AppendLine();


                        foreach (TableInfo2 table in tables)
                        {
                            if (table.IsWriteOnly)
                                continue;

                            game_db_dump.Append("dump_").Append(table.name).AppendLine("(File) -> ");
                            game_db_dump.Append("    io:format(\"dump ").Append(table.name).Append(" ... \"),").AppendLine();
                            game_db_dump.Append("    ok = file:write(File, <<\"DELETE FROM `").Append(table.name).Append("`;\\n\\n\">>),").AppendLine();


                            string tableCode = "";

                            if (table.CanFrag)
                            {
                                tableCode = "list_to_atom(\"t_" + table.name + "_\" ++ integer_to_list(I))";
                            }
                            else
                            {
                                tableCode = "t_" + table.name;
                            }

                            if (table.CanFrag)
                            {
                                game_db_dump.Append("    Size = lists:foldl(fun(I, S)-> S + ets:info(").Append(tableCode).Append(", size) end, 0, lists:seq(0, 99)),").AppendLine();

                                game_db_dump.AppendLine("    lists:foldl(fun(I, {S1, N1, L1})->");
                            }
                            else
                            {
                                game_db_dump.Append("    Size = ets:info(").Append(tableCode).Append(", size),").AppendLine();
                            }

                            game_db_dump.AppendLine("    ets:foldl(fun(Record, {S, N, L}) ->");

                            foreach (string column in table.columns)
                            {
                                game_db_dump.Append("        ");

                                string space = GenerateSpace(table.maxlength, FormatName(column).Length);

                                if (TableInfo2.IsIntegerType(table.types[column]))
                                    game_db_dump.AppendFormat("_{0}{3}= int_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);
                                else if (TableInfo2.IsFloatType(table.types[column]))
                                    game_db_dump.AppendFormat("_{0}{3}= rel_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);
                                else
                                    game_db_dump.AppendFormat("_{0}{3}= lst_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);

                                game_db_dump.AppendLine();
                            }

                            game_db_dump.Append(@"
        Last = if N == 100 orelse S + 1 == Size -> <<"");\n\n"">>; true -> <<""),\n"">> end,
");

                            game_db_dump.AppendLine();

                            game_db_dump.Append("        L2 = [<<\"(\"").AppendLine();

                            int n2 = 0;

                            foreach (string column in table.columns)
                            {
                                game_db_dump.AppendFormat("            , _{1}/binary", column, FormatName(column));

                                if (n2 < table.columns.Count - 1)
                                    game_db_dump.Append(", \",\"");

                                n2 += 1;

                                game_db_dump.AppendLine();
                            }

                            game_db_dump.Append(@"            , Last/binary
        >> | L],").AppendLine();

                            game_db_dump.AppendLine();

                            game_db_dump.Append("        if N == 100 orelse S + 1 == Size -> ").AppendLine();
                            game_db_dump.AppendFormat("            ok = file:write(File, [<<\"INSERT IGNORE INTO `{0}` (\"", table.name).AppendLine();

                            n2 = 0;

                            foreach (string column in table.columns)
                            {
                                game_db_dump.Append("                ,\"`").Append(column).Append("`");

                                if (n2 < table.columns.Count - 1)
                                    game_db_dump.Append(", ");

                                game_db_dump.AppendLine("\"");

                                n2 += 1;
                            }

                            game_db_dump.Append("            \") VALUES \\n\">> | lists:reverse(L2)]),").AppendLine();
                            game_db_dump.Append("            {S + 1, 0, []};").AppendLine();
                            game_db_dump.Append("        true ->").AppendLine();
                            game_db_dump.Append("            {S + 1, N + 1, L2}").AppendLine();
                            game_db_dump.Append("        end").AppendLine();


                            if (table.CanFrag)
                            {
                                game_db_dump.Append("    end, {S1, N1, L1}, ").Append(tableCode).Append(")");

                                game_db_dump.AppendLine();
                                game_db_dump.AppendLine("    end, {0, 0, []}, lists:seq(0, 99)),");
                            }
                            else
                            {
                                game_db_dump.Append("    end, {0, 0, []}, ").Append(tableCode).Append(")");

                                game_db_dump.AppendLine(",");
                            }

                            game_db_dump.Append("    ok = file:write(File, <<\"\\n\">>),").AppendLine();
                            game_db_dump.Append("    io:format(\"done~n\"),").AppendLine();
                            game_db_dump.Append("    ok.").AppendLine();
                            game_db_dump.AppendLine();
                        }

                        game_db_dump.Append(@"

lst_to_bin (null) ->
	<<""NULL"">>;
lst_to_bin (List) ->
	List2 = escape_str(List, []),
	Bin = list_to_binary(List2),
	<<""'"", Bin/binary, ""'"">>.
	
int_to_bin (null) ->
    <<""NULL"">>;
int_to_bin (Value) ->
    list_to_binary(integer_to_list(Value)).

rel_to_bin (null) ->
    <<""NULL"">>;
rel_to_bin (Value) when is_integer(Value) ->
    list_to_binary(integer_to_list(Value));
rel_to_bin (Value) ->
    list_to_binary(float_to_list(Value)).

escape_str ([], Result) ->
	lists:reverse(Result);
escape_str ([$' | String], Result) ->
	escape_str(String, [$' | [$\\ | Result]]);
escape_str ([$"" | String], Result) ->
	escape_str(String, [$"" | [$\\ | Result]]);
escape_str ([$\\ | String], Result) ->
	escape_str(String, [$\\ | [$\\ | Result]]);
escape_str ([$\n | String], Result) ->
	escape_str(String, [$n | [$\\ | Result]]);
escape_str ([$\r | String], Result) ->
	escape_str(String, [$r | [$\\ | Result]]);
escape_str ([Char | String], Result) ->
	escape_str(String, [Char | Result]).

");
                        using (StreamWriter writer = new StreamWriter(game_db_dump_file, false))
                        {
                            writer.Write(game_db_dump.ToString());
                        }
                    }
                    #endregion

                    #region game_db_save
                    {
                        StringBuilder game_db_save = new StringBuilder();

                        game_db_save.Append(@"-module(game_db_save).
-export([run/6]).

-include(""game.hrl"").
-include(""gen/game_db.hrl"").

run(Host, Port, User, Password, Database, OnlyPlayerTable)->
    {ok, Pid} = mysql_conn:start(Host, Port, User, Password, Database, fun(_,M,A) -> io:format(M, A) end),

    mysql_conn:fetch(Pid, [<<""/*!40101 SET NAMES utf8 */;\n"">>], self()),
    mysql_conn:fetch(Pid, [<<""/*!40101 SET SQL_MODE=''*/;\n"">>], self()),
    mysql_conn:fetch(Pid, [<<""/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n"">>], self()),
    mysql_conn:fetch(Pid, [<<""/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n"">>], self()),
    mysql_conn:fetch(Pid, [<<""/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n"">>], self()),
    mysql_conn:fetch(Pid, [<<""/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n"">>], self()),

");
                        foreach (TableInfo2 table in tables)
                        {
                            if (table.IsWriteOnly)
                                continue;

                            if (!table.name.StartsWith("player"))
                                game_db_save.Append("    if OnlyPlayerTable -> ok; true -> dump_").Append(table.name).AppendLine("(Pid) end,");
                            else
                                game_db_save.Append("    dump_").Append(table.name).AppendLine("(Pid),");
                        }

                        game_db_save.AppendLine("    mysql_conn:stop(Pid),");
                        game_db_save.AppendLine("    ok.");
                        game_db_save.AppendLine();

                        foreach (TableInfo2 table in tables)
                        {
                            if (table.IsWriteOnly)
                                continue;

                            game_db_save.Append("dump_").Append(table.name).AppendLine("(Pid) -> ");
                            game_db_save.Append("    io:format(\"save ").Append(table.name).Append(" ..\"),").AppendLine();
                            game_db_save.Append("    mysql_conn:fetch(Pid, [<<\"DELETE FROM `").Append(table.name).Append("`;\\n\\n\">>], self()),").AppendLine();
                            game_db_save.Append("    io:format(\". \"),").AppendLine();


                            string tableCode = "";

                            if (table.CanFrag)
                            {
                                tableCode = "list_to_atom(\"t_" + table.name + "_\" ++ integer_to_list(I))";
                            }
                            else
                            {
                                tableCode = "t_" + table.name;
                            }

                            if (table.CanFrag)
                            {
                                game_db_save.Append("    Size = lists:foldl(fun(I, S)-> S + ets:info(").Append(tableCode).Append(", size) end, 0, lists:seq(0, 99)),").AppendLine();

                                game_db_save.AppendLine("    lists:foldl(fun(I, {S1, N1, L1})->");
                            }
                            else
                            {
                                game_db_save.Append("    Size = ets:info(").Append(tableCode).Append(", size),").AppendLine();
                            }

                            game_db_save.AppendLine("    ets:foldl(fun(Record, {S, N, L}) ->");

                            foreach (string column in table.columns)
                            {
                                game_db_save.Append("        ");

                                string space = GenerateSpace(table.maxlength, FormatName(column).Length);

                                if (TableInfo2.IsIntegerType(table.types[column]))
                                    game_db_save.AppendFormat("_{0}{3}= int_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);
                                else if (TableInfo2.IsFloatType(table.types[column]))
                                    game_db_save.AppendFormat("_{0}{3}= rel_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);
                                else
                                    game_db_save.AppendFormat("_{0}{3}= lst_to_bin(Record #{1}.{2}),", FormatName(column), table.name, column, space);

                                game_db_save.AppendLine();
                            }

                            game_db_save.Append(@"
        Last = if N == 100 orelse S + 1 == Size -> <<"");\n\n"">>; true -> <<""),\n"">> end,
");

                            game_db_save.AppendLine();

                            game_db_save.Append("        L2 = [<<\"(\"").AppendLine();

                            int n2 = 0;

                            foreach (string column in table.columns)
                            {
                                game_db_save.AppendFormat("            , _{1}/binary", column, FormatName(column));

                                if (n2 < table.columns.Count - 1)
                                    game_db_save.Append(", \",\"");

                                n2 += 1;

                                game_db_save.AppendLine();
                            }

                            game_db_save.Append(@"            , Last/binary
        >> | L],").AppendLine();

                            game_db_save.AppendLine();

                            game_db_save.Append("        if N == 100 orelse S + 1 == Size -> ").AppendLine();
                            game_db_save.AppendFormat("            mysql_conn:fetch(Pid, [<<\"INSERT IGNORE INTO `{0}` (\"", table.name).AppendLine();

                            n2 = 0;

                            foreach (string column in table.columns)
                            {
                                game_db_save.Append("                ,\"`").Append(column).Append("`");

                                if (n2 < table.columns.Count - 1)
                                    game_db_save.Append(", ");

                                game_db_save.AppendLine("\"");

                                n2 += 1;
                            }

                            game_db_save.Append("            \") VALUES \\n\">> | lists:reverse(L2)], self()),").AppendLine();
                            game_db_save.Append("            {S + 1, 0, []};").AppendLine();
                            game_db_save.Append("        true ->").AppendLine();
                            game_db_save.Append("            {S + 1, N + 1, L2}").AppendLine();
                            game_db_save.Append("        end").AppendLine();


                            if (table.CanFrag)
                            {
                                game_db_save.Append("    end, {S1, N1, L1}, ").Append(tableCode).Append(")");

                                game_db_save.AppendLine();
                                game_db_save.AppendLine("    end, {0, 0, []}, lists:seq(0, 99)),");
                            }
                            else
                            {
                                game_db_save.Append("    end, {0, 0, []}, ").Append(tableCode).Append(")");

                                game_db_save.AppendLine(",");
                            }

                            game_db_save.Append("    io:format(\"done~n\"),").AppendLine();
                            game_db_save.Append("    ok.").AppendLine();
                            game_db_save.AppendLine();
                        }

                        game_db_save.Append(@"

lst_to_bin (null) ->
	<<""NULL"">>;
lst_to_bin (List) ->
	List2 = escape_str(List, []),
	Bin = list_to_binary(List2),
	<<""'"", Bin/binary, ""'"">>.
	
int_to_bin (null) ->
    <<""NULL"">>;
int_to_bin (Value) ->
    list_to_binary(integer_to_list(Value)).

rel_to_bin (null) ->
    <<""NULL"">>;
rel_to_bin (Value) when is_integer(Value) ->
    list_to_binary(integer_to_list(Value));
rel_to_bin (Value) ->
    list_to_binary(float_to_list(Value)).

escape_str ([], Result) ->
	lists:reverse(Result);
escape_str ([$' | String], Result) ->
	escape_str(String, [$' | [$\\ | Result]]);
escape_str ([$"" | String], Result) ->
	escape_str(String, [$"" | [$\\ | Result]]);
escape_str ([$\\ | String], Result) ->
	escape_str(String, [$\\ | [$\\ | Result]]);
escape_str ([$\n | String], Result) ->
	escape_str(String, [$n | [$\\ | Result]]);
escape_str ([$\r | String], Result) ->
	escape_str(String, [$r | [$\\ | Result]]);
escape_str ([Char | String], Result) ->
	escape_str(String, [Char | Result]).

");
                        using (StreamWriter writer = new StreamWriter(game_db_save_file, false))
                        {
                            writer.Write(game_db_save.ToString());
                        }
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
                return IsIntegerType(type) || IsFloatType(type);
            }

            public static bool IsFloatType(string type)
            {
                return type == "float" || type == "double";
            }

            public static bool IsIntegerType(string type)
            {
                return type == "int" || type == "bigint" || type == "tinyint" || type == "mediumint";
            }

            public bool IsWriteOnly
            {
                get
                {
                    return name == "player_ingot_change_record" ||
                        name == "level_up_record" ||
                        name == "player_war_report" ||
                        name == "player_item_change_record" ||
                        name == "player_item_change_record2" ||
                        name == "player_coin_change_record" ||
                        name == "player_coin_change_record2" ||
                        name == "player_fate_log" ||
                        name == "player_fate_log2" ||
                        name == "player_fame_log" ||
                        name == "player_role_exp_log" ||
                        name == "player_power_log" ||
                        name == "player_power_log2" ||
                        name == "player_take_bible_log" ||
                        name == "player_faction_join_faction_war_record" ||
                        name == "player_join_faction_war_record" ||
                        name == "player_defeat_world_boss_record" ||
                        name == "player_skill_log" ||
                        name == "player_farmland_log" ||
                        name == "player_coin_tree_count_log" ||
                        name == "player_flower_count_log" ||
                        name == "player_soul_change_record" ||
                        name == "player_soul_change_record2" ||
                        name == "player_soul_stone_change_record" ||
                        name == "player_elixir_log" ||
                        name == "player_peach_record" ||
                        name == "player_state_point_change_record" ||
                        name == "player_faction_roll_cake_faction_log" ||
                        name == "player_faction_roll_cake_member_log" ||
                        name == "player_server_war_log" ||
                        name == "player_aura_log" ||
                        name == "player_pet_animal_record" ||
                        name == "player_role_favor_record" ||
                        name == "player_item_attribute_stone_log" ||
                        name == "player_charge_lottery_log" ||
                        name == "player_online_shop_log" ||
                        name == "player_mysterious_shop_log" ||
                        name == "player_faction_contribution_log" ||
                        name == "player_faction_golden_room_log" ||
                        name == "player_spirit_log" ||
                        name == "player_long_yu_ling_log" ||
                        name == "player_xian_ling_log" ||
                        name == "player_xianling_tree_log" ||
                        name == "player_marry_favor_log" ||
                        name == "player_deploy_start_log" ||
                        name == "player_crystal_log" ||
                        name == "player_feats_log" ||
                        name == "player_xian_hua_log" ||
                        name == "player_pearl_log" ||
                        name == "player_ba_xian_ling_log" ||
                        name == "player_consume_jifen_log" ||
                        name == "player_dragonball_log" ||
                        name == "player_ling_yun_log" ||
                        name == "player_blood_pet_chip_log" ||
                        name == "player_blood_pet_log" ||
                        name == "player_neidan_log" ||
                        name == "player_dragon_egg_log" ||
                        name == "player_karma_change_record";
                }
            }

            public bool CanFrag
            {
                get
                {
                    return
                        name.StartsWith("player_") &&
                        ((name.StartsWith("player_faction") == false) || name.StartsWith("player_faction_quest")) &&
                        name != "player_friends" &&
                        name != "player_chat_record" &&
                        name != "player_lucky_shop_record" &&
                        name != "player_in_faction_battlefield" &&
                        name != "player_super_sport_ranking" &&
                        name != "player_super_sport_lucky_ranking" &&
                        columns.Contains(FragKey);
                }
            }

            public string FragKey
            {
                get
                {
                    if (name == "player_role_war_param")
                        return "master_player_id";

                    if (name == "player_role_fate")
                        return "player_role_id";

                    if (name == "player_monster_team_strategy")
                        return "mission_monster_team_id";

                    if (name == "player_banquet_material")
                        return "faction_id";

                    return "player_id";
                }
            }
        }
    }
}

