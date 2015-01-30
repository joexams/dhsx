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
		
		public static string FixPath(string path)
		{
			return path.Replace('\\', Path.DirectorySeparatorChar);
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
				protoSpecDir = FixPath(protoSpecDir);

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

        public static void GenerateFlashConfig (
            string enableDomainSelector, 
            string enableProtocolHelper, 
            string enableWarIgnoreButton,
            string gameServerDomain, 
            string gameServerPort,
            string gameVersion,
            string resourcesPathPrefix
        ) {
            string content = @"
package  
{
	/**
	 * 此文件是工具自动生成，请不要手工编辑！！！
	 * 此文件是工具自动生成，请不要手工编辑！！！
	 * 此文件是工具自动生成，请不要手工编辑！！！
	 * 此文件是工具自动生成，请不要手工编辑！！！
	 */
	public class Config 
	{
		//启用服务器IP选择框
		public static const EnableDomainSelector : Boolean = " + enableDomainSelector + @";
		
		//启用协议调试工具
		public static const EnableProtocolHelper : Boolean = " + enableProtocolHelper + @";
		
		//允许跳过战斗过程
		public static const EnableWarIgnoreButton : Boolean = " + enableWarIgnoreButton + @";
		
		//游戏服务器域名
		public static const GameServerDomain : String = """ + gameServerDomain + @""";
		
		//游戏服务器端口号
		public static const GameServerPort : Number = " + gameServerPort + @";

        //游戏版本号
        public static const GameVersion : String = """ + gameVersion + @""";

        //资源路径前缀
        public static const ResourcesPathPrefix : String = """ + resourcesPathPrefix + @""";
	}
}";

#if DEBUG
            string filename = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\client\\Config.as"));	
#else
            string filename = Path.Combine(Environment.CurrentDirectory, "client\\Config.as");
#endif

            filename = FixPath(filename);

            File.WriteAllText(filename, content);
        }
	}
}
