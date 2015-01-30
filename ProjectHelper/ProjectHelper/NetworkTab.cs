/*
 * Created by SharpDevelop.
 * User: BG5SBK
 * Date: 2010/9/10
 * Time: 17:24
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.Text;
using System.ComponentModel;
using System.Drawing;
using System.Windows.Forms;
using System.Net.Sockets;
using System.Collections.Generic;
using System.Text.RegularExpressions;
using System.Diagnostics;
using ProtoSpec;

namespace ProjectHelper
{
	/// <summary>
	/// Description of NetworkTab.
	/// </summary>
	public partial class NetworkTab : UserControl
	{
		public NetworkTab()
		{
			//
			// The InitializeComponent() call is required for Windows Forms designer support.
			//
			InitializeComponent();
			
			//
			// TODO: Add constructor code after the InitializeComponent() call.
			//
			
			scintilla1.ConfigurationManager.Language = "erlang";
			scintilla1.Margins[0].Width = 40;
		}
		
		void NetworkTabLoad(object sender, EventArgs e)
		{
			ParseProtoSpec(false);
			
			LoadTestScripts(false);
		}
		
		#region Protocol Debugger
		
		private bool isProtoSpecParsed = false;
		private ProtoSpecDocument protoSpecDocument = null;
		
		//private Dictionary<string, ProtoSpecAction> actionDict = null;
		
		public void ParseProtoSpec (bool reload)
		{
			if (isProtoSpecParsed && reload != true)
				return;

            try
            {
//				actionDict = new Dictionary<string, ProtoSpecAction>();
				
				#if DEBUG
				string protoSpecDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new\\doc\\通讯协议"));
				#else
				string protoSpecDir = Path.Combine(Environment.CurrentDirectory, "server-new\\doc\\通讯协议");
				#endif
				
				treeView1.Nodes.Clear();
					
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
					TreeNode moduleNode = treeView1.Nodes.Add(module.Name + " = " + module.ModuleId);
					
					foreach (ProtoSpecAction action in module.Actions)
					{
						TreeNode actionNode = moduleNode.Nodes.Add(action.Name + " = " + action.ActionId);
						
						actionNode.Tag = action;
						actionNode.ContextMenuStrip = contextMenuStrip1;
						
						//actionDict.Add(actionNode.FullPath, action);
						
						TreeNode inputNode = actionNode.Nodes.Add("in");
						
						ProtoSpecSubsetTree(module, action, inputNode, action.Input);
						
						TreeNode outputNode = actionNode.Nodes.Add("out");
						
						ProtoSpecSubsetTree(module, action, outputNode, action.Output);
					}
				}
				
				protoSpecDocument = document;
				
				isProtoSpecParsed = true;
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message, "出错", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
		}
		
		string ColumnTypeToString (ProtoSpecColumnType type)
		{
			string columnType = "";
			
			switch (type) 
			{
				case ProtoSpecColumnType.Byte:
					columnType = "byte";
					break;
					
				case ProtoSpecColumnType.Short:
					columnType = "short";
					break;
					
				case ProtoSpecColumnType.Int:
					columnType = "int";
					break;
					
				case ProtoSpecColumnType.Long:
					columnType = "long";
					break;
				
				case ProtoSpecColumnType.String:
					columnType = "string";
					break;
					
				case ProtoSpecColumnType.List:
					columnType = "list";
					break;
					
				case ProtoSpecColumnType.Enum:
					columnType = "enum";
					break;
			}
			
			return columnType;
		}
		
		void ProtoSpecSubsetTree (ProtoSpecModule module, ProtoSpecAction action, TreeNode parent, ProtoSpecSubset subset)
		{
			foreach (ProtoSpecColumn column in subset.Columns)
			{
				string columnType = ColumnTypeToString(column.ColumnType);
				
				string className = "";
				
				if (column.ClassName != null)
				{
					if (column.ClassModule != null)
						className = "<" + column.ClassModule + "." + column.ClassName + ">";
					else
						className = "<" + column.ClassName + ">";
				}
				
				TreeNode columnNode = parent.Nodes.Add(column.Name + " : " + columnType + className);
				
				if (column.Format != null)
				{
					ProtoSpecSubsetTree(module, action, columnNode, column.Format);
				}
				else if (column.Values != null)
				{
					foreach (ProtoSpecEnumValue value in column.Values)
					{
						columnNode.Nodes.Add(value.Name + " = " + value.Value);
					}
				}
			}
		}
		
		private Socket socket = null;
		
		bool OpenConnection ()
		{
			if (socket != null)
				return true;
			
			try
			{
				socket = new Socket(AddressFamily.InterNetwork, SocketType.Stream, ProtocolType.Tcp);
				
				socket.Connect(textBox2.Text, int.Parse(textBox3.Text));
				
				button2.Text = "断开";
				button2.Image = ProjectHelper.Resources.disconnect;
				
				return true;
			}
			catch (Exception ex)
			{
				socket = null;
				
				MessageBox.Show(ex.Message, "出错", MessageBoxButtons.OK, MessageBoxIcon.Error);
				
				return false;
			}
		}
		
		bool CloseConnection ()
		{
			if (socket == null)
				return false;
			
			button2.Text = "连接";
			button2.Image = ProjectHelper.Resources.connect;
			
			socket.Close();
			socket = null;
			
			return true;
		}
		
		void Button2Click(object sender, EventArgs e)
		{
			if (button2.Text == "连接")
			{
				OpenConnection();
			}
			else
			{
				CloseConnection();
			}
		}
		
		void TreeView1NodeMouseDoubleClick(object sender, TreeNodeMouseClickEventArgs e)
		{
			if (e.Node.Level != 1)
				return;
			
			ProtoSpecAction action = (ProtoSpecAction)e.Node.Tag; //actionDict[e.Node.FullPath];
			
			panel1.Controls.Clear();
			
			int label_x = 10;
			int label_y = 14;
			int submit_y = label_y;
			
			foreach (ProtoSpecColumn column in action.Input.Columns)
			{
				Label label = new Label();
				
				label.Text = column.Name;
				label.Left = label_x;
				label.Top = label_y;
				label.Height = 18;
				label.Width = panel1.Width - 20;
				label.Anchor = AnchorStyles.Left | AnchorStyles.Top | AnchorStyles.Right;
				
				panel1.Controls.Add(label);
				
				TextBox textbox = new TextBox();
				
				textbox.Name = "tb_" + column.Name;
				textbox.Left = label_x;
				textbox.Top = label.Top + label.Height;
				textbox.Width = panel1.Width - 20;
				textbox.Anchor = AnchorStyles.Left | AnchorStyles.Top | AnchorStyles.Right;
				
				label_y += label.Height + textbox.Height + 10;
				
				panel1.Controls.Add(textbox);
				
				submit_y = textbox.Top + textbox.Height + 20;
			}
			
			Button submit = new Button();
			
			submit.Text = "发送请求";
			submit.Left = label_x;
			submit.Top = submit_y;
			submit.Width = panel1.Width - 20;
			submit.Height = 34;
			submit.Anchor = AnchorStyles.Left | AnchorStyles.Top | AnchorStyles.Right;
			
			panel1.Controls.Add(submit);
			
			submit.Click += delegate(object sender2, EventArgs e2) 
			{
				try
				{
					SendData(action);
				}
				catch (SocketException socketExp)
				{
					CloseConnection();
					
					MessageBox.Show(socketExp.Message, "出错", MessageBoxButtons.OK, MessageBoxIcon.Error);
				}
				catch (Exception ex)
				{
					MessageBox.Show(ex.Message, "出错", MessageBoxButtons.OK, MessageBoxIcon.Error);
				}
			};
		}
		
		void SendData (ProtoSpecAction action)
		{
			if (OpenConnection() == false)
				return;
			
			treeView2.Nodes.Clear();
			
			MemoryStream stream = new MemoryStream();
			
			stream.WriteByte(0);
			stream.WriteByte(0);
			
			byte moduleId = byte.Parse(action.ParentModule.ModuleId);
			byte actionId = byte.Parse(action.ActionId);
			
			stream.WriteByte(moduleId);
			stream.WriteByte(actionId);
			
			foreach (ProtoSpecColumn column in action.Input.Columns)
			{
				TextBox textbox = (TextBox)panel1.Controls["tb_" + column.Name];
				
				switch (column.ColumnType) 
				{
                    case ProtoSpecColumnType.Enum:
					case ProtoSpecColumnType.Byte:
						stream.WriteByte(byte.Parse(textbox.Text));
						break;
						
					case ProtoSpecColumnType.Short:
						{
						short value = short.Parse(textbox.Text);
						
						byte[] bytes = BitConverter.GetBytes(value);
						
						stream.WriteByte(bytes[1]);
						stream.WriteByte(bytes[0]);
						}
						break;
						
					case ProtoSpecColumnType.Int:
						{
						int value = int.Parse(textbox.Text);
						
						byte[] bytes = BitConverter.GetBytes(value);
						
						stream.WriteByte(bytes[3]);
						stream.WriteByte(bytes[2]);
						stream.WriteByte(bytes[1]);
						stream.WriteByte(bytes[0]);
						}
						break;
						
					case ProtoSpecColumnType.Long:
						{
						long value = long.Parse(textbox.Text);
						
						byte[] bytes = BitConverter.GetBytes(value);
						
						stream.WriteByte(bytes[7]);
						stream.WriteByte(bytes[6]);
						stream.WriteByte(bytes[5]);
						stream.WriteByte(bytes[4]);
						stream.WriteByte(bytes[3]);
						stream.WriteByte(bytes[2]);
						stream.WriteByte(bytes[1]);
						stream.WriteByte(bytes[0]);
						}
						break;
					
					case ProtoSpecColumnType.String:
						{
						byte[] bytes = Encoding.UTF8.GetBytes(textbox.Text);
						
						short length = (short)bytes.Length;
						
						byte[] head = BitConverter.GetBytes(length);
						
						stream.WriteByte(head[1]);
						stream.WriteByte(head[0]);
						
						stream.Write(bytes, 0, bytes.Length);
						}
						break;
						
					case ProtoSpecColumnType.List:
						MessageBox.Show("请求参数中不支持list类型的数据");
						break;
				}
			}
			
			byte[] buffer = stream.ToArray();
			
			byte[] packHead = BitConverter.GetBytes((short)buffer.Length - 2);
			
			buffer[0] = packHead[1];
			buffer[1] = packHead[0];
			
			socket.Send(buffer);
			
			stream.Close();
			stream.Dispose();
			
			if (action.Output.Columns.Count > 0)
			{
			RECV:
				byte[] head = new byte[2];
				
				int received = 0;
				
				while (received != 2)
				{
					received += socket.Receive(head);
				}
				
				byte temp = head[0];
				
				head[0] = head[1];
				head[1] = temp;
				
				short length = BitConverter.ToInt16(head, 0);
				
				received = 0;
				
				byte[] data = new byte[length];
				
				while (received != length) 
				{
					received += socket.Receive(data, received, length - received, SocketFlags.None);
				}
				
				if (data[0] == moduleId && data[1] == actionId)
				{
					int parseOffset = 2;
				
					ParseResponse(data, ref parseOffset, action.Output, treeView2.Nodes);
				}
				else
				{
					goto RECV;
				}
			}
		}
		
		void ParseResponse (byte[] data, ref int parseOffset, ProtoSpecSubset format, TreeNodeCollection nodes)
		{
			foreach (ProtoSpecColumn column in format.Columns)
			{
				string columnName = column.Name + " : " + ColumnTypeToString(column.ColumnType) + " = ";
				
				switch (column.ColumnType) 
				{
					case ProtoSpecColumnType.Byte:
						columnName += data[parseOffset ++].ToString();
						break;
						
					case ProtoSpecColumnType.Short:
						{
							byte[] bytes = new byte[2];
							
							bytes[1] = data[parseOffset ++];
							bytes[0] = data[parseOffset ++];
							
							columnName += BitConverter.ToInt16(bytes, 0).ToString();
						}
						break;
						
					case ProtoSpecColumnType.Int:
						{
							byte[] bytes = new byte[4];
							
							bytes[3] = data[parseOffset ++];
							bytes[2] = data[parseOffset ++];
							bytes[1] = data[parseOffset ++];
							bytes[0] = data[parseOffset ++];
							
							columnName += BitConverter.ToInt32(bytes, 0).ToString();
						}
						break;
						
					case ProtoSpecColumnType.Long:
						{
							byte[] bytes = new byte[8];
							
							bytes[7] = data[parseOffset ++];
							bytes[6] = data[parseOffset ++];
							bytes[5] = data[parseOffset ++];
							bytes[4] = data[parseOffset ++];
							bytes[3] = data[parseOffset ++];
							bytes[2] = data[parseOffset ++];
							bytes[1] = data[parseOffset ++];
							bytes[0] = data[parseOffset ++];
							
							columnName += BitConverter.ToInt64(bytes, 0).ToString();
						}
						break;
					
					case ProtoSpecColumnType.String:
						{
							byte[] packhead = new byte[2];
							
							packhead[1] = data[parseOffset ++];
							packhead[0] = data[parseOffset ++];
							
							short len = BitConverter.ToInt16(packhead, 0);
							
							string text = Encoding.UTF8.GetString(data, parseOffset, len);
							
							parseOffset += len;
							
							columnName += "\"" + text + "\"";
						}
						break;
						
					case ProtoSpecColumnType.List:
						{
							byte[] packhead = new byte[2];
							
							packhead[1] = data[parseOffset ++];
							packhead[0] = data[parseOffset ++];
							
							short len = BitConverter.ToInt16(packhead, 0);
							
							columnName += len.ToString();
							
							TreeNode node = nodes.Add(columnName);
							
							for (int i = 0; i < len; i ++)
							{
								TreeNode subnode = node.Nodes.Add("[" + i + "]");
								
								ParseResponse(data, ref parseOffset, column.Format, subnode.Nodes);
							}
						}
						break;
						
					case ProtoSpecColumnType.Enum:
						{
							byte value = data[parseOffset ++];
							
							columnName += column.Values.GetNameByValue(value);
						}
						break;
				}
				
				if (column.ColumnType != ProtoSpecColumnType.List)
					nodes.Add(columnName);
			}
		}
		
		#endregion
		
		#region Action Head
		
		TreeNode selectedNode = null;
		
		void CopyErlangCodeToolStripMenuItemClick(object sender, EventArgs e)
		{
			//ProtoSpecAction action = (ProtoSpecAction)selectedNode.Tag;
			
			//string code = CodeGenerator.GenerateErlangApiHead(action);
			
			//Clipboard.SetText(code);
		}
		
		void TreeView1NodeMouseClick(object sender, TreeNodeMouseClickEventArgs e)
		{
			selectedNode = e.Node;
		}
		
		#endregion
			
		bool isTestScriptsLoaded = false;
		
		void LoadTestScripts (bool reload)
		{
			if (isTestScriptsLoaded && reload != true)
				return;
			
			#if DEBUG
			string testScriptDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new\\doc\\测试脚本"));
			#else
			string testScriptDir = Path.Combine(Environment.CurrentDirectory, "server-new\\doc\\测试脚本");
			#endif
			
			foreach (string dir in Directory.GetDirectories(testScriptDir))
			{
				string dirName = Path.GetFileName(dir);
				
				if (dirName == ".svn")
					continue;
				
				TreeNode node = treeView3.Nodes.Add(dirName.Substring(3));
				
				foreach (string file in Directory.GetFiles(dir))
				{
					TreeNode fileNode = node.Nodes.Add(Path.GetFileNameWithoutExtension(file));
					
					fileNode.Tag = file;
				}
			}
			
			isTestScriptsLoaded = true;
		}
		
		void TreeView3NodeMouseDoubleClick(object sender, TreeNodeMouseClickEventArgs e)
		{
			if (e.Node.Tag == null)
				return;
			
			if (tabControl1.SelectedIndex != 1)
				tabControl1.SelectedIndex = 1;
			
			scintilla1.Text = File.ReadAllText((string)e.Node.Tag);
		}
		
		void ToolStripButton2Click(object sender, EventArgs e)
		{
			TestScriptParser parser = new TestScriptParser(scintilla1.Text, 0);
			
			TestScriptDocument document = parser.Parse();
			
			if (OpenConnection())
			{
				TestScriptEvalContext context = new TestScriptEvalContext(protoSpecDocument, socket);
				
				TestScriptValue result = TestScriptEval.Do(document, context);
				
				treeView4.Nodes.Clear();
				
				foreach (TestScriptValue item in (TestScriptValueList)result.Value)
				{
					TreeNode node = treeView4.Nodes.Add(item.ModuleName + ":" + item.ActionName);
				
					TestScriptValue value = (TestScriptValue)item.Value;
					
					ProtoSpecModule module = protoSpecDocument.Modules.GetByName(item.ModuleName, true);
					
					ProtoSpecAction action = module.Actions.GetByName(item.ActionName, true);
					
					ParseResponse2(value, action.Output, node.Nodes);
				}
			}
		}
		
		void ParseResponse2 (TestScriptValue result, ProtoSpecSubset format, TreeNodeCollection nodes)
		{
			foreach (ProtoSpecColumn column in format.Columns)
			{
				string columnName = column.Name + " : " + ColumnTypeToString(column.ColumnType) + " = ";
				
				switch (column.ColumnType) 
				{
					case ProtoSpecColumnType.Byte:
					case ProtoSpecColumnType.Short:
					case ProtoSpecColumnType.Int:
					case ProtoSpecColumnType.Long:
						columnName += result.GetProperty(column.Name).Value;
						break;
					case ProtoSpecColumnType.Enum:
						columnName += result.GetProperty(column.Name).EnumName;
						break;
					case ProtoSpecColumnType.String:
						columnName += "\"" + result.GetProperty(column.Name).Value + "\"";
						break;
						
					case ProtoSpecColumnType.List:
						{
							TestScriptValueList list = (TestScriptValueList)result.GetProperty(column.Name).Value;
							
							columnName += list.Count;
							
							TreeNode node = nodes.Add(columnName);
							
							for (int i = 0; i < list.Count; i ++)
							{
								TreeNode subnode = node.Nodes.Add("[" + i + "]");
								
								ParseResponse2(list[i], column.Format, subnode.Nodes);
							}
						}
						break;
				}
				
				if (column.ColumnType != ProtoSpecColumnType.List)
					nodes.Add(columnName);
			}
		}
	}
}
