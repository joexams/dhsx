using System;
using System.IO;
using System.Configuration;

using HelperCore;
using System.Collections.Generic;

namespace ProjectHelper2
{
	class MainClass
	{
		static string mysql_hostname;
		static string mysql_username;
		static string mysql_password;
		static string mysql_database;
		static string mysql_port;

        static string enableDomainSelector;
        static string enableProtocolHelper;
        static string gameServerDomain;
        static string gameServerPort;

        static string gameVersion;
        static string resourcePathPrefix;
        static string enableWarIgnoreButton;

        static bool developMode;
	
		public static void Main (string[] args)
		{
#if DEBUG
        
		mysql_hostname = ConfigurationManager.AppSettings.Get("mysql_hostname");
		mysql_username = ConfigurationManager.AppSettings.Get("mysql_username");
		mysql_password = ConfigurationManager.AppSettings.Get("mysql_password");
		mysql_database = ConfigurationManager.AppSettings.Get("mysql_database");
		mysql_port     = ConfigurationManager.AppSettings.Get("mysql_port");

        enableDomainSelector = ConfigurationManager.AppSettings.Get("enable_domain_selector");
        enableProtocolHelper = ConfigurationManager.AppSettings.Get("enable_protocol_helper");
        gameServerDomain     = ConfigurationManager.AppSettings.Get("server_domain");
        gameServerPort       = ConfigurationManager.AppSettings.Get("server_port");
        gameVersion          = ConfigurationManager.AppSettings.Get("version");
        resourcePathPrefix   = ConfigurationManager.AppSettings.Get("resources_prefix");
        enableWarIgnoreButton = ConfigurationManager.AppSettings.Get("enable_war_ignore_button");

        developMode = bool.Parse(ConfigurationManager.AppSettings.Get("develop_mode"));
#else
            if (File.Exists("项目助手.config") == false)
            {
                File.WriteAllText("项目助手.config",
@"<?xml version=""1.0"" encoding=""utf-8""?>
<configuration>
  <appSettings>
    <!-- 程序版本号   -->
    <add key=""version"" value=""20110422"" />
    
    <!-- 资源路径前缀 -->
    <add key=""resources_prefix"" value=""./"" />

    <!-- 允许选择地址 -->
    <add key=""enable_domain_selector"" value=""true"" />
    
    <!-- 允许协议调试 -->
    <add key=""enable_protocol_helper"" value=""true"" />
    
    <!-- 允许调过战斗 -->
    <add key=""enable_war_ignore_button"" value=""true"" />
    
    <!-- 项目助手是否是开发模式 -->
    <add key=""develop_mode"" value=""true"" />

    <!-- 服务器 -->
    <add key=""server_domain""   value=""localhost"" />
    <add key=""server_port""     value=""8888"" />

    <!-- 数据库 -->
    <add key=""mysql_hostname"" value=""localhost"" />
    <add key=""mysql_username"" value=""root"" />
    <add key=""mysql_password"" value=""ybybyb"" />
    <add key=""mysql_database"" value=""gamedb"" />
    <add key=""mysql_port""     value=""3306"" />
  </appSettings>
</configuration>");
            }

        ExeConfigurationFileMap configFileMap = new ExeConfigurationFileMap();

        configFileMap.ExeConfigFilename = "项目助手.config";
        configFileMap.LocalUserConfigFilename = "项目助手.config";
        configFileMap.RoamingUserConfigFilename = "项目助手.config";

        Configuration config = ConfigurationManager.OpenMappedExeConfiguration(
            configFileMap, ConfigurationUserLevel.None
        );

        mysql_hostname = config.AppSettings.Settings["mysql_hostname"].Value;
        mysql_username = config.AppSettings.Settings["mysql_username"].Value;
        mysql_password = config.AppSettings.Settings["mysql_password"].Value;
        mysql_database = config.AppSettings.Settings["mysql_database"].Value;
        mysql_port = config.AppSettings.Settings["mysql_port"].Value;

        enableDomainSelector = config.AppSettings.Settings["enable_domain_selector"].Value;
        enableProtocolHelper = config.AppSettings.Settings["enable_protocol_helper"].Value;
        gameServerDomain     = config.AppSettings.Settings["server_domain"].Value;
        gameServerPort       = config.AppSettings.Settings["server_port"].Value;
        gameVersion          = config.AppSettings.Settings["version"].Value;
        resourcePathPrefix   = config.AppSettings.Settings["resources_prefix"].Value;
        enableWarIgnoreButton = config.AppSettings.Settings["enable_war_ignore_button"].Value;

        developMode = bool.Parse(config.AppSettings.Settings["develop_mode"].Value);
#endif

        try
        {
               
#if DEBUG 
                string configPath = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\config.txt"));

#else
                string configPath = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "config.txt"));
#endif

                //Console.WriteLine(File.ReadAllText(configPath));

                CodeGenerator.SetMessageHandler(
					delegate(CodeGenerator.MessageType type, string message){
						Console.WriteLine(message);
					}
				);
				
				bool exit = false;

                if (args.Length == 1 && args[0] == "auto")
                {
                    CodeGenerator.GenerateFlashCode(true);
                    CodeGenerator.GenerateErlangCode2(true, developMode);
                    CodeGenerator.GenerateFlashDatabaseCode2(true, mysql_hostname, mysql_username, mysql_password, mysql_database, mysql_port);
                    CodeGenerator.GenerateErlangDatabaseCode(true, mysql_hostname, mysql_username, mysql_password, mysql_database, mysql_port, developMode);
                    CodeGenerator.GenerateFlashConfig(
                        enableDomainSelector,
                        enableProtocolHelper,
                        enableWarIgnoreButton,
                        gameServerDomain,
                        gameServerPort,
                        gameVersion,
                        resourcePathPrefix
                    );
                    CodeGenerator.GenerateKeywordCodes();
                    CodeGenerator.GenerateAreaCodes();

                    BuildServerProject();
                    BuildClientProject();

                    return;
                }

				while (!exit) 
				{
                    Console.WriteLine("请选择一个操作：");
					Console.WriteLine("  1 - 生成代码");
					Console.WriteLine("  2 - 编译项目");
                    Console.WriteLine("  3 - 启动服务器");
                    Console.WriteLine("  4 - 更新数据库");
                    Console.WriteLine("  5 - 导出数据库");
					Console.WriteLine("  x - 退出");
                    /*
					Console.WriteLine("【1】一步到位（SVN更新 -> 升级数据库 -> 生成代码 -> 编译项目）");
                    Console.WriteLine("【2】导出数据（SVN更新 -> 升级数据库 -> 导出数据 -> SVN提交）");
                    Console.WriteLine("【3】启动服务");
					Console.WriteLine("【q】退出助手");
                    */
                    Console.Write("> ");
				
					string input = Console.ReadLine();
					
					Console.Clear();
					
					switch (input) 
					{
						case "1": 
							CodeGenerator.GenerateFlashCode(true);
							CodeGenerator.GenerateErlangCode2(true, developMode);
						    CodeGenerator.GenerateFlashDatabaseCode2(true, mysql_hostname, mysql_username, mysql_password, mysql_database, mysql_port);
						    CodeGenerator.GenerateErlangDatabaseCode(true, mysql_hostname, mysql_username, mysql_password, mysql_database, mysql_port, developMode);
                            CodeGenerator.GenerateFlashConfig(
                                enableDomainSelector, 
                                enableProtocolHelper, 
                                enableWarIgnoreButton,
                                gameServerDomain, 
                                gameServerPort, 
                                gameVersion, 
                                resourcePathPrefix
                            );
                            CodeGenerator.GenerateKeywordCodes();
							CodeGenerator.GenerateAreaCodes();
							break;
						
						case "2": 
							BuildServerProject();
							BuildClientProject();
							break;
						
						case "3": 
							StartServer();
							break;
						
						case "4": 
							//CodeGenerator.GenerateFlashCode(true);
							//CodeGenerator.GenerateErlangCode2(true);
							break;
						
						case "5": 
						    //CodeGenerator.GenerateFlashDatabaseCode2(true, mysql_hostname, mysql_username, mysql_password, mysql_database, mysql_port);
						    //CodeGenerator.GenerateErlangDatabaseCode(true, mysql_hostname, mysql_username, mysql_password, mysql_database, mysql_port);
							break;
						
						case "x":
							exit = true;
							break;
					}
					
					Console.WriteLine();
				}
            }
            catch(Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }
		}
		
		private static void SettingMySQL()
		{
			//Console.Clear();
			
			Console.WriteLine("**根据下面提示输入数据库服务器连接参数，不修改当前值请直接回车**");
			Console.WriteLine("");
			
			Console.Write("地址(" + mysql_hostname + "):");
			
			string input = Console.ReadLine();
			
			if (input.Trim() != string.Empty)
				mysql_hostname = input.Trim();
			
			Console.Write("账号(" + mysql_username + "):");
			
			input = Console.ReadLine();
			
			if (input.Trim() != string.Empty)
				mysql_username = input.Trim();
			
			Console.Write("密码(" + mysql_password + "):");
			
			input = Console.ReadLine();
			
			if (input.Trim() != string.Empty)
				mysql_password = input.Trim();
			
			Console.Write("库名(" + mysql_database + "):");
			
			input = Console.ReadLine();
			
			if (input.Trim() != string.Empty)
				mysql_database = input.Trim();
			
			Console.Write("端口(" + mysql_port + "):");
			
			input = Console.ReadLine();
			
			if (input.Trim() != string.Empty)
				mysql_port = input.Trim();
		}
		
		private static void StartServer()
		{
#if DEBUG
        	string workingDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new"));
#else
			string workingDir = Path.Combine(Environment.CurrentDirectory, "server-new");
#endif
			
			System.Diagnostics.Process proc = new System.Diagnostics.Process();
			
			proc.StartInfo.WorkingDirectory = workingDir;
			proc.StartInfo.FileName = "start.bat";
			proc.Start();
		}
		
		private static void Execute(string filename, string arguments, string workingDir)
		{
			System.Diagnostics.Process proc = new System.Diagnostics.Process();
			
			proc.StartInfo.WorkingDirectory = workingDir;
			proc.StartInfo.FileName = filename;
			proc.StartInfo.Arguments = arguments;
			proc.StartInfo.CreateNoWindow = true;
			proc.StartInfo.UseShellExecute = false;
			proc.StartInfo.RedirectStandardOutput = true;
			proc.Start();
			
			while (!proc.HasExited)
			{
				Console.WriteLine(proc.StandardOutput.ReadLine());
			}
		}
		
		private static void BuildServerProject()
		{
#if DEBUG
        	string workingDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new\\ebin"));
#else
			string workingDir = Path.Combine(Environment.CurrentDirectory, "server-new\\ebin");
#endif
			
			Execute("erl", "-noshell -s make all -s init stop", workingDir);
		}

        private static void BuildClientProject()
		{
#if DEBUG
        	string workingDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\client"));
#else
			string workingDir = Path.Combine(Environment.CurrentDirectory, "client");
#endif

            Execute("mxmlc", "Index.as", workingDir);
            Execute("mxmlc", "Main.as", workingDir);
            Execute("mxmlc", "Templet.as -output ./assets/templet.swf", workingDir);
            Execute("mxmlc", "StrategyWar.as -output assets/strategy_war/" + gameVersion + ".swf", workingDir);

            System.Diagnostics.Process proc = new System.Diagnostics.Process();

#if DEBUG
            proc.StartInfo.WorkingDirectory = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\tool\\collection");
#else
            proc.StartInfo.WorkingDirectory = Path.Combine(Environment.CurrentDirectory, "tool\\collection");
#endif
            proc.StartInfo.FileName = "php";
            proc.StartInfo.Arguments = "collection.php";
            proc.StartInfo.CreateNoWindow = true;
            proc.StartInfo.UseShellExecute = false;
            proc.StartInfo.RedirectStandardError = true;
            proc.StartInfo.RedirectStandardOutput = true;
            proc.Start();

            while (!proc.HasExited)
            {
                //Console.WriteLine(proc.StandardOutput.ReadLine());
            }
		}

        private static Dictionary<string, string> ParseConfig(string config)
        {
            Dictionary<string, string> result = new Dictionary<string, string>();

            int line = 0;

            int idStart = 0;

            string lastId = null;

            bool inCommit = false;

            bool lookForIdEnd = false;
            bool lookForEqui  = false;
            bool lookForValue = false;
            bool inSingleQuote = false;

            for (int i = 0; i < config.Length; i ++)
            {
                char c = config[i];

                if (c == '\r' || c == '\n')
                {
                    if (i < config.Length - 1)
                    {
                        char peek = config[i + 1];

                        if (peek == '\r' || c == '\n')
                            i += 1;
                    }

                    line += 1;

                    inCommit = false;

                    continue;
                }

                if (inCommit)
                    continue;

                if (c == '%')
                {
                    if (lookForIdEnd)
                        throw new Exception("不正确的设置名称格式");

                    inCommit = true;

                    continue;
                }

                if (lookForIdEnd == false)
                {
                    if (char.IsLetterOrDigit(c))
                    {
                        lookForIdEnd = true;
                        idStart = i;
                        continue;
                    }
                    else if (c == ' ' || c == '\t')
                    {
                        continue;
                    }
                    else
                    {
                        throw new Exception("LINE " + line + "：非法字符'" + c + "'");
                    }
                }
                else
                {
                    if (char.IsLetterOrDigit(c))
                    {
                        continue;
                    }
                    else if (c == ' ' || c == '\t')
                    {
                        lastId = config.Substring(idStart, i - idStart);

                        lookForEqui = true;

                        continue;
                    }
                }
            }

            return result;
        }
	}
}

