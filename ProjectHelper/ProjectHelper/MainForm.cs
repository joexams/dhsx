/*
 * Created by SharpDevelop.
 * User: BG5SBK
 * Date: 2010/9/9
 * Time: 17:01
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.Collections.Generic;
using System.Drawing;
using System.Windows.Forms;
using System.Diagnostics;
using System.Text.RegularExpressions;
using HelperCore;

namespace ProjectHelper
{
	/// <summary>
	/// Description of MainForm.
	/// </summary>
	public partial class MainForm : Form
	{
		public MainForm()
		{
			//
			// The InitializeComponent() call is required for Windows Forms designer support.
			//
			InitializeComponent();
			
			//
			// TODO: Add constructor code after the InitializeComponent() call.
			//

            CodeGenerator.SetMessageHandler(delegate(CodeGenerator.MessageType type, string message) {
                if (type == CodeGenerator.MessageType.Info)
                    MessageBox.Show(message, "提示", MessageBoxButtons.OK, MessageBoxIcon.Information);
                else
                    MessageBox.Show(message, "错误", MessageBoxButtons.OK, MessageBoxIcon.Error);
            });
		}
	
		#if DEBUG
		string serverDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server"));
		string serverDir2 = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new"));
		string clientDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\client"));
		#else
		string serverDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "server"));
		string serverDir2 = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "server-new"));
		string clientDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "client"));
		#endif
		
//		Process gatewayProc = null;
		Process serverProc =  null;
		
		
		void ToolStripMenuItem1Click(object sender, EventArgs e)
		{
			networkTab1.ParseProtoSpec(true);
		}
		
		void ToolStripMenuItem2Click(object sender, EventArgs e)
		{
			CodeGenerator.GenerateFlashCode(false);
			
			//CodeGenerator.GenerateErlangCode(false);
			CodeGenerator.GenerateErlangCode2(false);
		}
		
		void ToolStripMenuItem3Click(object sender, EventArgs e)
		{
//			if (gatewayProc != null)
//			{
//				try {
//					gatewayProc.CloseMainWindow();
//					gatewayProc.WaitForExit(2000);
//				} catch (Exception) {
//					
//				}
//			}
			
			if (serverProc != null)
			{
				try {
					serverProc.CloseMainWindow();
					serverProc.WaitForExit(2000);
				} catch (Exception) {
					
				}
			}
			
//			gatewayProc = new Process();
//			
//			gatewayProc.StartInfo.FileName = Path.Combine(serverDir, "start_gateway.bat");
//			gatewayProc.StartInfo.WorkingDirectory = serverDir;
//			
//			gatewayProc.Start();
//			
//			System.Threading.Thread.Sleep(200);
			
			serverProc = new Process();
			
			serverProc.StartInfo.FileName = Path.Combine(serverDir2, "start.bat");
			serverProc.StartInfo.WorkingDirectory = serverDir2;
			
			serverProc.Start();
		}
		
		void ToolStripMenuItem4Click(object sender, EventArgs e)
		{
			Process serverBuildProc = new Process();
			
			serverBuildProc.StartInfo.FileName = Path.Combine(serverDir2, "build.bat");
			serverBuildProc.StartInfo.WorkingDirectory = serverDir2;
			
			serverBuildProc.Start();
			
			Process clientBuildProc = new Process();
			
			clientBuildProc.StartInfo.FileName = Path.Combine(clientDir, "build.bat");
			clientBuildProc.StartInfo.WorkingDirectory = clientDir;
			
			clientBuildProc.Start();
		}
		
		void ToolStripMenuItem5Click(object sender, EventArgs e)
		{
			Process proc = new Process();
			
			proc.StartInfo.FileName = Path.Combine(serverDir2, "clear.bat");
			proc.StartInfo.WorkingDirectory = serverDir2;
			
			proc.Start();
		}
	
		void ToolStripButton6Click(object sender, EventArgs e)
		{
			Process.Start(serverDir2);
		}
		
		void GenerateSql(object sender, EventArgs e)
		{
			databaseTab1.GenerateSql();
		}
		
		void DeployDatabase(object sender, EventArgs e)
		{
			databaseTab1.DeployDatabase();
		}
		
		void GenerateDbCode(object sender, EventArgs e)
		{
			databaseTab1.GenerateDbCode();
		}
		
		static Regex formatNameRegex = new Regex("^(.)|_(.)");
		
		string FormatName (string name)
		{
			return formatNameRegex.Replace(name, delegate(Match match){
			    if (match.Groups[1].Success)
				    return char.ToUpper(match.Groups[1].Value[0]).ToString();
			   	else
				    return char.ToUpper(match.Groups[2].Value[0]).ToString();
			});
		}
		
		void TabControl1SelectedIndexChanged(object sender, EventArgs e)
		{
			if (tabControl1.SelectedIndex == 0)
			{
				toolStripButton1.Visible = true;
				toolStripButton2.Visible = true;
				
				toolStripButton9.Visible = false;
			}
			else
			{
				toolStripButton1.Visible = false;
				toolStripButton2.Visible = false;
				
				toolStripButton9.Visible = true;
			}
		}

        private void button1_Click(object sender, EventArgs e)
        {
            KeywordBuilder.GenerateKeywordCodes();
        }
	}
}
