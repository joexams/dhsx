/*
 * Created by SharpDevelop.
 * User: BG5SBK
 * Date: 2010/9/15
 * Time: 9:48
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.ComponentModel;
using System.Drawing;
using System.Windows.Forms;
using System.Diagnostics;

namespace ProjectHelper
{
	/// <summary>
	/// Description of ErlangConsole.
	/// </summary>
	public partial class ErlangConsole : UserControl
	{
		public ErlangConsole()
		{
			//
			// The InitializeComponent() call is required for Windows Forms designer support.
			//
			InitializeComponent();
			
			//
			// TODO: Add constructor code after the InitializeComponent() call.
			//
		}
	
		#if DEBUG
		string serverDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server"));
		#else
		string serverDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "server"));
		#endif
		
		Process gatewayProc = null;
		
		void StartGateway_ToolStripMenuItemClick(object sender, EventArgs e)
		{
			if (gatewayProc != null)
			{
				try {
					gatewayProc.StandardInput.WriteLine("q().");
				} catch (Exception) {
					
				}
			}
			
			Process process = new Process();
			
			process.StartInfo.FileName = Path.Combine(serverDir, "start_gateway.bat");
			//process.StartInfo.CreateNoWindow = true;
			process.StartInfo.UseShellExecute = false;
			process.StartInfo.RedirectStandardInput = true;
			//process.StartInfo.RedirectStandardOutput = true;
			process.StartInfo.WorkingDirectory = serverDir;
			
			process.Start();
			
			gatewayProc = process;
		}
		
		void Timer1Tick(object sender, EventArgs e)
		{
			if (gatewayProc != null)
			{
				textBox1.Clear();
				
				textBox1.Text = gatewayProc.StandardOutput.ReadToEnd();
			}
		}
		
		void Button1Click(object sender, EventArgs e)
		{
			if (gatewayProc != null)
			{
				gatewayProc.StandardInput.Write(textBox2.Text);
				
				textBox2.Clear();
			}
		}
	}
}
