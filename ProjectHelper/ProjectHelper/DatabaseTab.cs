/*
 * Created by SharpDevelop.
 * User: BG5SBK
 * Date: 2010/9/9
 * Time: 17:14
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.ComponentModel;
using System.Drawing;
using System.Windows.Forms;
using System.IO;
using System.Text;
using MySql.Data.MySqlClient;
using System.Collections.Generic;
using System.Text.RegularExpressions;
using System.Runtime.InteropServices;
using ScintillaNet;
using HelperCore;

namespace ProjectHelper
{
	public delegate void WindowProcedureHandle(Message uMsg);
	
	public class MyNativeWindow : NativeWindow
	{
		public event WindowProcedureHandle WindowProcedure;
		
		public MyNativeWindow(IntPtr handle)
		{
			base.AssignHandle(handle);
		}
		
		protected override void WndProc(ref Message m)
		{
			base.WndProc(ref m);
			
			WindowProcedure(m);
		}
		
		public void SendMessage (ref Message m)
		{
			base.WndProc(ref m);
		}
	}
	
	/// <summary>
	/// Description of DatabaseTab.
	/// </summary>
	public partial class DatabaseTab : UserControl
	{
		public DatabaseTab()
		{
			InitializeComponent();
			
			scintilla1.ConfigurationManager.Language = "mssql";
			scintilla1.Margins[0].Width = 48;
			
			scintilla1.Lexing.Keywords[0] = scintilla1.Lexing.Keywords[0].Replace(" level", "") + @"
auto_increment
comment
engine
character";
			
			scintilla1.Lexing.Keywords[4] = scintilla1.Lexing.Keywords[3].Replace(" sign", "");
		}
		
		public void GenerateSql()
		{
			try
			{
				string outputFile = DatabaseDeployer.CombineSql(checkBox1.Checked, textBox4.Text);
				
				scintilla1.Text = File.ReadAllText(outputFile);
				
				//MessageBox.Show("文件路径：" + outputFile, "完成", MessageBoxButtons.OK, MessageBoxIcon.Information);
			}
			catch (Exception ex)
			{
				MessageBox.Show("出错了：" + ex.Message, "出错", MessageBoxButtons.OK, MessageBoxIcon.Error);
			}
		}
		
		public void DeployDatabase()
		{
			if (DialogResult.No == MessageBox.Show("部署数据库会导致数据库清空，你确定要部署吗？", "警告", MessageBoxButtons.YesNo, MessageBoxIcon.Information))
			{
				return;
			}
			
			GenerateSql();
			
			DatabaseDeployer.Deploy(textBox1.Text, textBox2.Text, textBox3.Text, textBox4.Text, textBox5.Text, false);
		}
		
		public void GenerateDbCode()
		{
            //CodeGenerator.GenerateErlangDatabaseCode(
            //    false,
            //    textBox1.Text, textBox2.Text, textBox3.Text, textBox4.Text, textBox5.Text
            //);
			
			CodeGenerator.GenerateFlashDatabaseCode2(
				false,
				textBox1.Text, textBox2.Text, textBox3.Text, textBox4.Text, textBox5.Text
			);
			
			CodeGenerator.GenerateErlangDatabaseCode2(
				false,
				textBox1.Text, textBox2.Text, textBox3.Text, textBox4.Text, textBox5.Text
			);
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
	}
}