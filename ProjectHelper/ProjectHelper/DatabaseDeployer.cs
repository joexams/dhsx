/*
 * Created by SharpDevelop.
 * User: BG5SBK
 * Date: 2010/9/27
 * Time: 10:12
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.Text;
using System.Windows.Forms;
using MySql.Data.MySqlClient;

namespace ProjectHelper
{
	/// <summary>
	/// Description of DatabaseDeployer.
	/// </summary>
	public class DatabaseDeployer
	{
		public static string CombineSql (bool reCreateDatabase, string database)
		{
			string newline = "\r\n";
			
			#if DEBUG
			string databaseDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\database"));
			#else
			string databaseDir = Path.Combine(Environment.CurrentDirectory, "database");
			#endif
			
			string dataFile = Path.Combine(databaseDir, "data.sql");
			string indexFile = Path.Combine(databaseDir, "index.txt");
			string outputFile = Path.Combine(databaseDir, "output.sql");
			
			using (StreamWriter writer = new StreamWriter(outputFile, false))
			{
				if (reCreateDatabase)
				{
					writer.Write("DROP DATABASE IF EXISTS `" + database + "`;\r\n\r\nCREATE DATABASE `" + database + "` CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';\r\n\r\nUSE `" + database + "`;\r\n\r\nSET NAMES utf8;\r\n\r\n");
					writer.Write(newline);
					writer.Write(newline);
				}
				
				string[] tableList = File.ReadAllText(indexFile, Encoding.UTF8).Replace("\r", "").Split('\n');
				
				foreach (string table in tableList)
				{
					string tableName = table.Trim();
					
					if (string.IsNullOrEmpty(tableName))
						continue;
					
					string tableSql = Path.Combine(databaseDir, "table\\" + tableName + ".sql");

                    if (File.Exists(tableSql) == false)
                        throw new Exception("找不到" + tableName + "的SQL脚本文件");

					writer.Write(File.ReadAllText(tableSql));
					writer.Write(newline);
					writer.Write(newline);
				}
				
				writer.Write(File.ReadAllText(dataFile));
			}
			
			return outputFile;
		}
		
		public static bool Deploy (string server, string uid, string pwd, string database, string port, bool isConsole)
		{
			try
			{
				string outputFile = CombineSql(true, database);
				
				string connectionString = "Server=" + server + ";Uid=" + uid + ";Pwd=" + pwd + ";Port=" + port + ";Charset=utf8";
				
				using (MySqlConnection connection = new MySqlConnection(connectionString))
				{
					connection.Open();
					
					string sql = File.ReadAllText(outputFile);
					
					string[] sqlList = System.Text.RegularExpressions.Regex.Split(sql, ";\\r\\n");
					
					foreach (string cmd in sqlList)
					{
						if (string.IsNullOrEmpty(cmd.Trim()))
							continue;
						
						using (MySqlCommand command = new MySqlCommand(cmd, connection))
						{
							command.ExecuteNonQuery();
						}
					}
					
					connection.Clone();
				}
				
				if (isConsole)
					Console.WriteLine("部署数据库完毕");
				else
					MessageBox.Show("数据库部署完毕", "完成", MessageBoxButtons.OK, MessageBoxIcon.Information);
				
				return true;
			}
			catch (Exception ex)
			{
				if (isConsole)
					Console.WriteLine("部署数据库出错：" + ex.Message);
				else
					MessageBox.Show("出错了：" + ex.Message, "出错", MessageBoxButtons.OK, MessageBoxIcon.Error);
				
				return false;
			}
		}
	}
}
