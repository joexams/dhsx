/*
 * Created by SharpDevelop.
 * User: BG5SBK
 * Date: 2010/9/15
 * Time: 9:48
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
namespace ProjectHelper
{
	partial class ErlangConsole
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
			this.splitContainer1 = new System.Windows.Forms.SplitContainer();
			this.splitContainer2 = new System.Windows.Forms.SplitContainer();
			this.textBox1 = new System.Windows.Forms.TextBox();
			this.button1 = new System.Windows.Forms.Button();
			this.textBox2 = new System.Windows.Forms.TextBox();
			this.menuStrip1 = new System.Windows.Forms.MenuStrip();
			this.BuildAll_ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
			this.编译项目ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
			this.CleanAll_ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
			this.进程ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
			this.StartGateway_ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
			this.StartServer_ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
			this.timer1 = new System.Windows.Forms.Timer(this.components);
			this.splitContainer1.Panel1.SuspendLayout();
			this.splitContainer1.SuspendLayout();
			this.splitContainer2.Panel1.SuspendLayout();
			this.splitContainer2.Panel2.SuspendLayout();
			this.splitContainer2.SuspendLayout();
			this.menuStrip1.SuspendLayout();
			this.SuspendLayout();
			// 
			// splitContainer1
			// 
			this.splitContainer1.Dock = System.Windows.Forms.DockStyle.Fill;
			this.splitContainer1.Location = new System.Drawing.Point(0, 25);
			this.splitContainer1.Name = "splitContainer1";
			// 
			// splitContainer1.Panel1
			// 
			this.splitContainer1.Panel1.Controls.Add(this.splitContainer2);
			this.splitContainer1.Size = new System.Drawing.Size(849, 476);
			this.splitContainer1.SplitterDistance = 405;
			this.splitContainer1.TabIndex = 0;
			// 
			// splitContainer2
			// 
			this.splitContainer2.Dock = System.Windows.Forms.DockStyle.Fill;
			this.splitContainer2.Location = new System.Drawing.Point(0, 0);
			this.splitContainer2.Name = "splitContainer2";
			this.splitContainer2.Orientation = System.Windows.Forms.Orientation.Horizontal;
			// 
			// splitContainer2.Panel1
			// 
			this.splitContainer2.Panel1.Controls.Add(this.textBox1);
			// 
			// splitContainer2.Panel2
			// 
			this.splitContainer2.Panel2.Controls.Add(this.button1);
			this.splitContainer2.Panel2.Controls.Add(this.textBox2);
			this.splitContainer2.Size = new System.Drawing.Size(405, 476);
			this.splitContainer2.SplitterDistance = 325;
			this.splitContainer2.TabIndex = 0;
			// 
			// textBox1
			// 
			this.textBox1.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
			this.textBox1.Dock = System.Windows.Forms.DockStyle.Fill;
			this.textBox1.Location = new System.Drawing.Point(0, 0);
			this.textBox1.Multiline = true;
			this.textBox1.Name = "textBox1";
			this.textBox1.ScrollBars = System.Windows.Forms.ScrollBars.Vertical;
			this.textBox1.Size = new System.Drawing.Size(405, 325);
			this.textBox1.TabIndex = 0;
			// 
			// button1
			// 
			this.button1.Location = new System.Drawing.Point(334, 108);
			this.button1.Margin = new System.Windows.Forms.Padding(10);
			this.button1.Name = "button1";
			this.button1.Size = new System.Drawing.Size(61, 29);
			this.button1.TabIndex = 1;
			this.button1.Text = "GO";
			this.button1.UseVisualStyleBackColor = true;
			this.button1.Click += new System.EventHandler(this.Button1Click);
			// 
			// textBox2
			// 
			this.textBox2.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
			this.textBox2.Location = new System.Drawing.Point(0, 0);
			this.textBox2.Margin = new System.Windows.Forms.Padding(0);
			this.textBox2.Multiline = true;
			this.textBox2.Name = "textBox2";
			this.textBox2.ScrollBars = System.Windows.Forms.ScrollBars.Vertical;
			this.textBox2.Size = new System.Drawing.Size(405, 98);
			this.textBox2.TabIndex = 0;
			// 
			// menuStrip1
			// 
			this.menuStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
									this.BuildAll_ToolStripMenuItem,
									this.进程ToolStripMenuItem});
			this.menuStrip1.Location = new System.Drawing.Point(0, 0);
			this.menuStrip1.Name = "menuStrip1";
			this.menuStrip1.Size = new System.Drawing.Size(849, 25);
			this.menuStrip1.TabIndex = 1;
			this.menuStrip1.Text = "menuStrip1";
			// 
			// BuildAll_ToolStripMenuItem
			// 
			this.BuildAll_ToolStripMenuItem.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
									this.编译项目ToolStripMenuItem,
									this.CleanAll_ToolStripMenuItem});
			this.BuildAll_ToolStripMenuItem.Name = "BuildAll_ToolStripMenuItem";
			this.BuildAll_ToolStripMenuItem.Size = new System.Drawing.Size(49, 21);
			this.BuildAll_ToolStripMenuItem.Text = "Build";
			// 
			// 编译项目ToolStripMenuItem
			// 
			this.编译项目ToolStripMenuItem.Name = "编译项目ToolStripMenuItem";
			this.编译项目ToolStripMenuItem.Size = new System.Drawing.Size(124, 22);
			this.编译项目ToolStripMenuItem.Text = "编译项目";
			// 
			// CleanAll_ToolStripMenuItem
			// 
			this.CleanAll_ToolStripMenuItem.Name = "CleanAll_ToolStripMenuItem";
			this.CleanAll_ToolStripMenuItem.Size = new System.Drawing.Size(124, 22);
			this.CleanAll_ToolStripMenuItem.Text = "清理项目";
			// 
			// 进程ToolStripMenuItem
			// 
			this.进程ToolStripMenuItem.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
									this.StartGateway_ToolStripMenuItem,
									this.StartServer_ToolStripMenuItem});
			this.进程ToolStripMenuItem.Name = "进程ToolStripMenuItem";
			this.进程ToolStripMenuItem.Size = new System.Drawing.Size(59, 21);
			this.进程ToolStripMenuItem.Text = "Debug";
			// 
			// StartGateway_ToolStripMenuItem
			// 
			this.StartGateway_ToolStripMenuItem.Name = "StartGateway_ToolStripMenuItem";
			this.StartGateway_ToolStripMenuItem.Size = new System.Drawing.Size(173, 22);
			this.StartGateway_ToolStripMenuItem.Text = "重启Gateway进程";
			this.StartGateway_ToolStripMenuItem.Click += new System.EventHandler(this.StartGateway_ToolStripMenuItemClick);
			// 
			// StartServer_ToolStripMenuItem
			// 
			this.StartServer_ToolStripMenuItem.Name = "StartServer_ToolStripMenuItem";
			this.StartServer_ToolStripMenuItem.Size = new System.Drawing.Size(173, 22);
			this.StartServer_ToolStripMenuItem.Text = "重启Server进程";
			// 
			// timer1
			// 
			this.timer1.Enabled = true;
			this.timer1.Tick += new System.EventHandler(this.Timer1Tick);
			// 
			// ErlangConsole
			// 
			this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 12F);
			this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
			this.Controls.Add(this.splitContainer1);
			this.Controls.Add(this.menuStrip1);
			this.Name = "ErlangConsole";
			this.Size = new System.Drawing.Size(849, 501);
			this.splitContainer1.Panel1.ResumeLayout(false);
			this.splitContainer1.ResumeLayout(false);
			this.splitContainer2.Panel1.ResumeLayout(false);
			this.splitContainer2.Panel1.PerformLayout();
			this.splitContainer2.Panel2.ResumeLayout(false);
			this.splitContainer2.Panel2.PerformLayout();
			this.splitContainer2.ResumeLayout(false);
			this.menuStrip1.ResumeLayout(false);
			this.menuStrip1.PerformLayout();
			this.ResumeLayout(false);
			this.PerformLayout();
		}
		private System.Windows.Forms.Button button1;
		private System.Windows.Forms.Timer timer1;
		private System.Windows.Forms.ToolStripMenuItem CleanAll_ToolStripMenuItem;
		private System.Windows.Forms.ToolStripMenuItem BuildAll_ToolStripMenuItem;
		private System.Windows.Forms.ToolStripMenuItem StartServer_ToolStripMenuItem;
		private System.Windows.Forms.ToolStripMenuItem StartGateway_ToolStripMenuItem;
		private System.Windows.Forms.ToolStripMenuItem 编译项目ToolStripMenuItem;
		private System.Windows.Forms.ToolStripMenuItem 进程ToolStripMenuItem;
		private System.Windows.Forms.MenuStrip menuStrip1;
		private System.Windows.Forms.TextBox textBox2;
		private System.Windows.Forms.TextBox textBox1;
		private System.Windows.Forms.SplitContainer splitContainer2;
		private System.Windows.Forms.SplitContainer splitContainer1;
	}
}
