/*
 * Created by SharpDevelop.
 * User: BG5SBK
 * Date: 2010/9/10
 * Time: 17:24
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
namespace ProjectHelper
{
	partial class NetworkTab
	{
		/// <summary>
		/// Designer variable used to keep track of non-visual components.
		/// </summary>
		private System.ComponentModel.IContainer components = null;
		
		/// <summary>
		/// Disposes resources used by the control.
		/// </summary>
		/// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
		protected override void Dispose(bool disposing)
		{
			if (disposing) {
				if (components != null) {
					components.Dispose();
				}
			}
			base.Dispose(disposing);
		}
		
		/// <summary>
		/// This method is required for Windows Forms designer support.
		/// Do not change the method contents inside the source code editor. The Forms designer might
		/// not be able to load this method if it was changed manually.
		/// </summary>
		private void InitializeComponent()
		{
			this.components = new System.ComponentModel.Container();
			this.groupBox1 = new System.Windows.Forms.GroupBox();
			this.panel3 = new System.Windows.Forms.Panel();
			this.treeView1 = new System.Windows.Forms.TreeView();
			this.groupBox2 = new System.Windows.Forms.GroupBox();
			this.button2 = new System.Windows.Forms.Button();
			this.textBox3 = new System.Windows.Forms.TextBox();
			this.label2 = new System.Windows.Forms.Label();
			this.textBox2 = new System.Windows.Forms.TextBox();
			this.label1 = new System.Windows.Forms.Label();
			this.treeView2 = new System.Windows.Forms.TreeView();
			this.splitContainer1 = new System.Windows.Forms.SplitContainer();
			this.panel1 = new System.Windows.Forms.Panel();
			this.splitContainer2 = new System.Windows.Forms.SplitContainer();
			this.splitContainer3 = new System.Windows.Forms.SplitContainer();
			this.groupBox3 = new System.Windows.Forms.GroupBox();
			this.panel4 = new System.Windows.Forms.Panel();
			this.treeView3 = new System.Windows.Forms.TreeView();
			this.tabControl1 = new System.Windows.Forms.TabControl();
			this.tabPage1 = new System.Windows.Forms.TabPage();
			this.tabPage2 = new System.Windows.Forms.TabPage();
			this.splitContainer4 = new System.Windows.Forms.SplitContainer();
			this.panel2 = new System.Windows.Forms.Panel();
			this.scintilla1 = new ScintillaNet.Scintilla();
			this.toolStrip1 = new System.Windows.Forms.ToolStrip();
			this.toolStripButton2 = new System.Windows.Forms.ToolStripButton();
			this.panel5 = new System.Windows.Forms.Panel();
			this.treeView4 = new System.Windows.Forms.TreeView();
			this.contextMenuStrip1 = new System.Windows.Forms.ContextMenuStrip(this.components);
			this.CopyErlangCodeToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
			this.groupBox1.SuspendLayout();
			this.panel3.SuspendLayout();
			this.groupBox2.SuspendLayout();
			this.splitContainer1.Panel1.SuspendLayout();
			this.splitContainer1.Panel2.SuspendLayout();
			this.splitContainer1.SuspendLayout();
			this.splitContainer2.Panel1.SuspendLayout();
			this.splitContainer2.Panel2.SuspendLayout();
			this.splitContainer2.SuspendLayout();
			this.splitContainer3.Panel1.SuspendLayout();
			this.splitContainer3.Panel2.SuspendLayout();
			this.splitContainer3.SuspendLayout();
			this.groupBox3.SuspendLayout();
			this.panel4.SuspendLayout();
			this.tabControl1.SuspendLayout();
			this.tabPage1.SuspendLayout();
			this.tabPage2.SuspendLayout();
			this.splitContainer4.Panel1.SuspendLayout();
			this.splitContainer4.Panel2.SuspendLayout();
			this.splitContainer4.SuspendLayout();
			this.panel2.SuspendLayout();
			((System.ComponentModel.ISupportInitialize)(this.scintilla1)).BeginInit();
			this.toolStrip1.SuspendLayout();
			this.panel5.SuspendLayout();
			this.contextMenuStrip1.SuspendLayout();
			this.SuspendLayout();
			// 
			// groupBox1
			// 
			this.groupBox1.Controls.Add(this.panel3);
			this.groupBox1.Dock = System.Windows.Forms.DockStyle.Fill;
			this.groupBox1.Location = new System.Drawing.Point(0, 0);
			this.groupBox1.Margin = new System.Windows.Forms.Padding(0, 0, 0, 10);
			this.groupBox1.Name = "groupBox1";
			this.groupBox1.Padding = new System.Windows.Forms.Padding(10);
			this.groupBox1.Size = new System.Drawing.Size(275, 188);
			this.groupBox1.TabIndex = 0;
			this.groupBox1.TabStop = false;
			this.groupBox1.Text = "通讯协议";
			// 
			// panel3
			// 
			this.panel3.BorderStyle = System.Windows.Forms.BorderStyle.Fixed3D;
			this.panel3.Controls.Add(this.treeView1);
			this.panel3.Dock = System.Windows.Forms.DockStyle.Fill;
			this.panel3.Location = new System.Drawing.Point(10, 24);
			this.panel3.Name = "panel3";
			this.panel3.Size = new System.Drawing.Size(255, 154);
			this.panel3.TabIndex = 3;
			// 
			// treeView1
			// 
			this.treeView1.BorderStyle = System.Windows.Forms.BorderStyle.None;
			this.treeView1.Dock = System.Windows.Forms.DockStyle.Fill;
			this.treeView1.Location = new System.Drawing.Point(0, 0);
			this.treeView1.Margin = new System.Windows.Forms.Padding(0, 0, 0, 10);
			this.treeView1.Name = "treeView1";
			this.treeView1.Size = new System.Drawing.Size(251, 150);
			this.treeView1.TabIndex = 2;
			this.treeView1.NodeMouseDoubleClick += new System.Windows.Forms.TreeNodeMouseClickEventHandler(this.TreeView1NodeMouseDoubleClick);
			this.treeView1.NodeMouseClick += new System.Windows.Forms.TreeNodeMouseClickEventHandler(this.TreeView1NodeMouseClick);
			// 
			// groupBox2
			// 
			this.groupBox2.Controls.Add(this.button2);
			this.groupBox2.Controls.Add(this.textBox3);
			this.groupBox2.Controls.Add(this.label2);
			this.groupBox2.Controls.Add(this.textBox2);
			this.groupBox2.Controls.Add(this.label1);
			this.groupBox2.Dock = System.Windows.Forms.DockStyle.Top;
			this.groupBox2.Location = new System.Drawing.Point(0, 0);
			this.groupBox2.Margin = new System.Windows.Forms.Padding(0, 0, 0, 10);
			this.groupBox2.Name = "groupBox2";
			this.groupBox2.Padding = new System.Windows.Forms.Padding(10);
			this.groupBox2.Size = new System.Drawing.Size(275, 63);
			this.groupBox2.TabIndex = 1;
			this.groupBox2.TabStop = false;
			this.groupBox2.Text = "连接设置";
			// 
			// button2
			// 
			this.button2.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
			this.button2.Image = global::ProjectHelper.Resources.connect;
			this.button2.ImageAlign = System.Drawing.ContentAlignment.MiddleRight;
			this.button2.Location = new System.Drawing.Point(197, 18);
			this.button2.Margin = new System.Windows.Forms.Padding(6, 0, 0, 0);
			this.button2.Name = "button2";
			this.button2.Size = new System.Drawing.Size(68, 35);
			this.button2.TabIndex = 2;
			this.button2.Text = "连接";
			this.button2.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText;
			this.button2.UseVisualStyleBackColor = true;
			this.button2.Click += new System.EventHandler(this.Button2Click);
			// 
			// textBox3
			// 
			this.textBox3.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
			this.textBox3.Location = new System.Drawing.Point(149, 26);
			this.textBox3.Margin = new System.Windows.Forms.Padding(0);
			this.textBox3.Name = "textBox3";
			this.textBox3.Size = new System.Drawing.Size(42, 21);
			this.textBox3.TabIndex = 1;
			this.textBox3.Text = "8888";
			// 
			// label2
			// 
			this.label2.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
			this.label2.Location = new System.Drawing.Point(115, 23);
			this.label2.Margin = new System.Windows.Forms.Padding(10, 0, 3, 0);
			this.label2.Name = "label2";
			this.label2.Size = new System.Drawing.Size(31, 23);
			this.label2.TabIndex = 0;
			this.label2.Text = "端口";
			this.label2.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// textBox2
			// 
			this.textBox2.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
									| System.Windows.Forms.AnchorStyles.Right)));
			this.textBox2.Location = new System.Drawing.Point(43, 25);
			this.textBox2.Margin = new System.Windows.Forms.Padding(0, 0, 0, 10);
			this.textBox2.Name = "textBox2";
			this.textBox2.Size = new System.Drawing.Size(62, 21);
			this.textBox2.TabIndex = 1;
			this.textBox2.Text = "localhost";
			// 
			// label1
			// 
			this.label1.Location = new System.Drawing.Point(10, 23);
			this.label1.Name = "label1";
			this.label1.Size = new System.Drawing.Size(30, 23);
			this.label1.TabIndex = 0;
			this.label1.Text = "地址";
			this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// treeView2
			// 
			this.treeView2.Dock = System.Windows.Forms.DockStyle.Fill;
			this.treeView2.Location = new System.Drawing.Point(4, 6);
			this.treeView2.Name = "treeView2";
			this.treeView2.Size = new System.Drawing.Size(583, 184);
			this.treeView2.TabIndex = 0;
			// 
			// splitContainer1
			// 
			this.splitContainer1.Dock = System.Windows.Forms.DockStyle.Fill;
			this.splitContainer1.Location = new System.Drawing.Point(0, 0);
			this.splitContainer1.Margin = new System.Windows.Forms.Padding(0);
			this.splitContainer1.Name = "splitContainer1";
			this.splitContainer1.Orientation = System.Windows.Forms.Orientation.Horizontal;
			// 
			// splitContainer1.Panel1
			// 
			this.splitContainer1.Panel1.Controls.Add(this.panel1);
			// 
			// splitContainer1.Panel2
			// 
			this.splitContainer1.Panel2.Controls.Add(this.treeView2);
			this.splitContainer1.Panel2.Padding = new System.Windows.Forms.Padding(4, 6, 6, 6);
			this.splitContainer1.Size = new System.Drawing.Size(593, 442);
			this.splitContainer1.SplitterDistance = 242;
			this.splitContainer1.TabIndex = 3;
			// 
			// panel1
			// 
			this.panel1.Dock = System.Windows.Forms.DockStyle.Fill;
			this.panel1.Location = new System.Drawing.Point(0, 0);
			this.panel1.Name = "panel1";
			this.panel1.Size = new System.Drawing.Size(593, 242);
			this.panel1.TabIndex = 0;
			// 
			// splitContainer2
			// 
			this.splitContainer2.Dock = System.Windows.Forms.DockStyle.Fill;
			this.splitContainer2.FixedPanel = System.Windows.Forms.FixedPanel.Panel1;
			this.splitContainer2.Location = new System.Drawing.Point(10, 10);
			this.splitContainer2.Name = "splitContainer2";
			// 
			// splitContainer2.Panel1
			// 
			this.splitContainer2.Panel1.Controls.Add(this.groupBox2);
			this.splitContainer2.Panel1.Controls.Add(this.splitContainer3);
			// 
			// splitContainer2.Panel2
			// 
			this.splitContainer2.Panel2.Controls.Add(this.tabControl1);
			this.splitContainer2.Size = new System.Drawing.Size(886, 468);
			this.splitContainer2.SplitterDistance = 275;
			this.splitContainer2.SplitterWidth = 10;
			this.splitContainer2.TabIndex = 7;
			// 
			// splitContainer3
			// 
			this.splitContainer3.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
									| System.Windows.Forms.AnchorStyles.Left) 
									| System.Windows.Forms.AnchorStyles.Right)));
			this.splitContainer3.Location = new System.Drawing.Point(0, 73);
			this.splitContainer3.Margin = new System.Windows.Forms.Padding(0);
			this.splitContainer3.Name = "splitContainer3";
			this.splitContainer3.Orientation = System.Windows.Forms.Orientation.Horizontal;
			// 
			// splitContainer3.Panel1
			// 
			this.splitContainer3.Panel1.Controls.Add(this.groupBox1);
			// 
			// splitContainer3.Panel2
			// 
			this.splitContainer3.Panel2.Controls.Add(this.groupBox3);
			this.splitContainer3.Size = new System.Drawing.Size(275, 395);
			this.splitContainer3.SplitterDistance = 188;
			this.splitContainer3.TabIndex = 3;
			// 
			// groupBox3
			// 
			this.groupBox3.Controls.Add(this.panel4);
			this.groupBox3.Dock = System.Windows.Forms.DockStyle.Fill;
			this.groupBox3.Location = new System.Drawing.Point(0, 0);
			this.groupBox3.Margin = new System.Windows.Forms.Padding(0);
			this.groupBox3.Name = "groupBox3";
			this.groupBox3.Padding = new System.Windows.Forms.Padding(10);
			this.groupBox3.Size = new System.Drawing.Size(275, 203);
			this.groupBox3.TabIndex = 2;
			this.groupBox3.TabStop = false;
			this.groupBox3.Text = "测试脚本";
			// 
			// panel4
			// 
			this.panel4.BorderStyle = System.Windows.Forms.BorderStyle.Fixed3D;
			this.panel4.Controls.Add(this.treeView3);
			this.panel4.Dock = System.Windows.Forms.DockStyle.Fill;
			this.panel4.Location = new System.Drawing.Point(10, 24);
			this.panel4.Name = "panel4";
			this.panel4.Size = new System.Drawing.Size(255, 169);
			this.panel4.TabIndex = 1;
			// 
			// treeView3
			// 
			this.treeView3.BorderStyle = System.Windows.Forms.BorderStyle.None;
			this.treeView3.Dock = System.Windows.Forms.DockStyle.Fill;
			this.treeView3.Location = new System.Drawing.Point(0, 0);
			this.treeView3.Name = "treeView3";
			this.treeView3.Size = new System.Drawing.Size(251, 165);
			this.treeView3.TabIndex = 0;
			this.treeView3.NodeMouseDoubleClick += new System.Windows.Forms.TreeNodeMouseClickEventHandler(this.TreeView3NodeMouseDoubleClick);
			// 
			// tabControl1
			// 
			this.tabControl1.Controls.Add(this.tabPage1);
			this.tabControl1.Controls.Add(this.tabPage2);
			this.tabControl1.Dock = System.Windows.Forms.DockStyle.Fill;
			this.tabControl1.Location = new System.Drawing.Point(0, 0);
			this.tabControl1.Margin = new System.Windows.Forms.Padding(0);
			this.tabControl1.Multiline = true;
			this.tabControl1.Name = "tabControl1";
			this.tabControl1.SelectedIndex = 0;
			this.tabControl1.Size = new System.Drawing.Size(601, 468);
			this.tabControl1.TabIndex = 4;
			// 
			// tabPage1
			// 
			this.tabPage1.Controls.Add(this.splitContainer1);
			this.tabPage1.Location = new System.Drawing.Point(4, 22);
			this.tabPage1.Margin = new System.Windows.Forms.Padding(0);
			this.tabPage1.Name = "tabPage1";
			this.tabPage1.Size = new System.Drawing.Size(593, 442);
			this.tabPage1.TabIndex = 0;
			this.tabPage1.Text = "协议调试";
			this.tabPage1.UseVisualStyleBackColor = true;
			// 
			// tabPage2
			// 
			this.tabPage2.Controls.Add(this.splitContainer4);
			this.tabPage2.Location = new System.Drawing.Point(4, 22);
			this.tabPage2.Margin = new System.Windows.Forms.Padding(0);
			this.tabPage2.Name = "tabPage2";
			this.tabPage2.Size = new System.Drawing.Size(593, 442);
			this.tabPage2.TabIndex = 1;
			this.tabPage2.Text = "测试脚本";
			this.tabPage2.UseVisualStyleBackColor = true;
			// 
			// splitContainer4
			// 
			this.splitContainer4.Dock = System.Windows.Forms.DockStyle.Fill;
			this.splitContainer4.FixedPanel = System.Windows.Forms.FixedPanel.Panel2;
			this.splitContainer4.Location = new System.Drawing.Point(0, 0);
			this.splitContainer4.Name = "splitContainer4";
			this.splitContainer4.Orientation = System.Windows.Forms.Orientation.Horizontal;
			// 
			// splitContainer4.Panel1
			// 
			this.splitContainer4.Panel1.Controls.Add(this.panel2);
			this.splitContainer4.Panel1.Controls.Add(this.toolStrip1);
			// 
			// splitContainer4.Panel2
			// 
			this.splitContainer4.Panel2.Controls.Add(this.panel5);
			this.splitContainer4.Size = new System.Drawing.Size(593, 442);
			this.splitContainer4.SplitterDistance = 241;
			this.splitContainer4.TabIndex = 0;
			// 
			// panel2
			// 
			this.panel2.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
									| System.Windows.Forms.AnchorStyles.Left) 
									| System.Windows.Forms.AnchorStyles.Right)));
			this.panel2.BorderStyle = System.Windows.Forms.BorderStyle.Fixed3D;
			this.panel2.Controls.Add(this.scintilla1);
			this.panel2.Location = new System.Drawing.Point(0, 29);
			this.panel2.Margin = new System.Windows.Forms.Padding(0);
			this.panel2.Name = "panel2";
			this.panel2.Size = new System.Drawing.Size(593, 212);
			this.panel2.TabIndex = 3;
			// 
			// scintilla1
			// 
			this.scintilla1.Dock = System.Windows.Forms.DockStyle.Fill;
			this.scintilla1.Location = new System.Drawing.Point(0, 0);
			this.scintilla1.Margin = new System.Windows.Forms.Padding(0);
			this.scintilla1.Name = "scintilla1";
			this.scintilla1.Size = new System.Drawing.Size(589, 208);
			this.scintilla1.Styles.BraceBad.FontName = "Verdana";
			this.scintilla1.Styles.BraceLight.FontName = "Verdana";
			this.scintilla1.Styles.ControlChar.FontName = "Verdana";
			this.scintilla1.Styles.Default.FontName = "Verdana";
			this.scintilla1.Styles.IndentGuide.FontName = "Verdana";
			this.scintilla1.Styles.LastPredefined.FontName = "Verdana";
			this.scintilla1.Styles.LineNumber.FontName = "Verdana";
			this.scintilla1.Styles.Max.FontName = "Verdana";
			this.scintilla1.TabIndex = 2;
			// 
			// toolStrip1
			// 
			this.toolStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
									this.toolStripButton2});
			this.toolStrip1.Location = new System.Drawing.Point(0, 0);
			this.toolStrip1.Margin = new System.Windows.Forms.Padding(0, 0, 0, 2);
			this.toolStrip1.Name = "toolStrip1";
			this.toolStrip1.Padding = new System.Windows.Forms.Padding(0, 4, 1, 0);
			this.toolStrip1.Size = new System.Drawing.Size(593, 28);
			this.toolStrip1.TabIndex = 1;
			this.toolStrip1.Text = "toolStrip1";
			// 
			// toolStripButton2
			// 
			this.toolStripButton2.Image = global::ProjectHelper.Resources.resultset_next;
			this.toolStripButton2.ImageTransparentColor = System.Drawing.Color.Magenta;
			this.toolStripButton2.Name = "toolStripButton2";
			this.toolStripButton2.Size = new System.Drawing.Size(52, 21);
			this.toolStripButton2.Text = "运行";
			this.toolStripButton2.ToolTipText = "运行";
			this.toolStripButton2.Click += new System.EventHandler(this.ToolStripButton2Click);
			// 
			// panel5
			// 
			this.panel5.BorderStyle = System.Windows.Forms.BorderStyle.Fixed3D;
			this.panel5.Controls.Add(this.treeView4);
			this.panel5.Dock = System.Windows.Forms.DockStyle.Fill;
			this.panel5.Location = new System.Drawing.Point(0, 0);
			this.panel5.Name = "panel5";
			this.panel5.Size = new System.Drawing.Size(593, 197);
			this.panel5.TabIndex = 1;
			// 
			// treeView4
			// 
			this.treeView4.BorderStyle = System.Windows.Forms.BorderStyle.None;
			this.treeView4.Dock = System.Windows.Forms.DockStyle.Fill;
			this.treeView4.Location = new System.Drawing.Point(0, 0);
			this.treeView4.Name = "treeView4";
			this.treeView4.Size = new System.Drawing.Size(589, 193);
			this.treeView4.TabIndex = 0;
			// 
			// contextMenuStrip1
			// 
			this.contextMenuStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
									this.CopyErlangCodeToolStripMenuItem});
			this.contextMenuStrip1.Name = "contextMenuStrip1";
			this.contextMenuStrip1.Size = new System.Drawing.Size(174, 26);
			// 
			// CopyErlangCodeToolStripMenuItem
			// 
			this.CopyErlangCodeToolStripMenuItem.Name = "CopyErlangCodeToolStripMenuItem";
			this.CopyErlangCodeToolStripMenuItem.Size = new System.Drawing.Size(173, 22);
			this.CopyErlangCodeToolStripMenuItem.Text = "复制Erlang代码段";
			this.CopyErlangCodeToolStripMenuItem.Click += new System.EventHandler(this.CopyErlangCodeToolStripMenuItemClick);
			// 
			// NetworkTab
			// 
			this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 12F);
			this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
			this.Controls.Add(this.splitContainer2);
			this.Name = "NetworkTab";
			this.Padding = new System.Windows.Forms.Padding(10);
			this.Size = new System.Drawing.Size(906, 488);
			this.Load += new System.EventHandler(this.NetworkTabLoad);
			this.groupBox1.ResumeLayout(false);
			this.panel3.ResumeLayout(false);
			this.groupBox2.ResumeLayout(false);
			this.groupBox2.PerformLayout();
			this.splitContainer1.Panel1.ResumeLayout(false);
			this.splitContainer1.Panel2.ResumeLayout(false);
			this.splitContainer1.ResumeLayout(false);
			this.splitContainer2.Panel1.ResumeLayout(false);
			this.splitContainer2.Panel2.ResumeLayout(false);
			this.splitContainer2.ResumeLayout(false);
			this.splitContainer3.Panel1.ResumeLayout(false);
			this.splitContainer3.Panel2.ResumeLayout(false);
			this.splitContainer3.ResumeLayout(false);
			this.groupBox3.ResumeLayout(false);
			this.panel4.ResumeLayout(false);
			this.tabControl1.ResumeLayout(false);
			this.tabPage1.ResumeLayout(false);
			this.tabPage2.ResumeLayout(false);
			this.splitContainer4.Panel1.ResumeLayout(false);
			this.splitContainer4.Panel1.PerformLayout();
			this.splitContainer4.Panel2.ResumeLayout(false);
			this.splitContainer4.ResumeLayout(false);
			this.panel2.ResumeLayout(false);
			((System.ComponentModel.ISupportInitialize)(this.scintilla1)).EndInit();
			this.toolStrip1.ResumeLayout(false);
			this.toolStrip1.PerformLayout();
			this.panel5.ResumeLayout(false);
			this.contextMenuStrip1.ResumeLayout(false);
			this.ResumeLayout(false);
		}
		private System.Windows.Forms.Panel panel5;
		private System.Windows.Forms.Panel panel4;
		private System.Windows.Forms.Panel panel3;
		private System.Windows.Forms.Panel panel2;
		private ScintillaNet.Scintilla scintilla1;
		private System.Windows.Forms.SplitContainer splitContainer3;
		private System.Windows.Forms.GroupBox groupBox3;
		private System.Windows.Forms.TreeView treeView4;
		private System.Windows.Forms.ToolStripButton toolStripButton2;
		private System.Windows.Forms.ToolStrip toolStrip1;
		private System.Windows.Forms.SplitContainer splitContainer4;
		private System.Windows.Forms.TreeView treeView3;
		private System.Windows.Forms.Panel panel1;
		private System.Windows.Forms.TabPage tabPage2;
		private System.Windows.Forms.TabPage tabPage1;
		private System.Windows.Forms.TabControl tabControl1;
		private System.Windows.Forms.ToolStripMenuItem CopyErlangCodeToolStripMenuItem;
		private System.Windows.Forms.ContextMenuStrip contextMenuStrip1;
		private System.Windows.Forms.SplitContainer splitContainer2;
		private System.Windows.Forms.TreeView treeView2;
		private System.Windows.Forms.SplitContainer splitContainer1;
		private System.Windows.Forms.Button button2;
		private System.Windows.Forms.TreeView treeView1;
		private System.Windows.Forms.Label label1;
		private System.Windows.Forms.TextBox textBox2;
		private System.Windows.Forms.Label label2;
		private System.Windows.Forms.TextBox textBox3;
		private System.Windows.Forms.GroupBox groupBox2;
		private System.Windows.Forms.GroupBox groupBox1;
	}
}
