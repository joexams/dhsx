/*
 * Created by SharpDevelop.
 * User: BG5SBK
 * Date: 2010/9/9
 * Time: 17:14
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
namespace ProjectHelper
{
	partial class DatabaseTab
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
			this.textBox1 = new System.Windows.Forms.TextBox();
			this.label1 = new System.Windows.Forms.Label();
			this.label2 = new System.Windows.Forms.Label();
			this.textBox2 = new System.Windows.Forms.TextBox();
			this.label3 = new System.Windows.Forms.Label();
			this.textBox3 = new System.Windows.Forms.TextBox();
			this.textBox4 = new System.Windows.Forms.TextBox();
			this.label4 = new System.Windows.Forms.Label();
			this.checkBox1 = new System.Windows.Forms.CheckBox();
			this.panel1 = new System.Windows.Forms.Panel();
			this.scintilla1 = new ScintillaNet.Scintilla();
			this.label5 = new System.Windows.Forms.Label();
			this.textBox5 = new System.Windows.Forms.TextBox();
			this.panel2 = new System.Windows.Forms.Panel();
			this.panel1.SuspendLayout();
			((System.ComponentModel.ISupportInitialize)(this.scintilla1)).BeginInit();
			this.panel2.SuspendLayout();
			this.SuspendLayout();
			// 
			// textBox1
			// 
			this.textBox1.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
									| System.Windows.Forms.AnchorStyles.Right)));
			this.textBox1.Location = new System.Drawing.Point(80, 10);
			this.textBox1.Margin = new System.Windows.Forms.Padding(0, 0, 0, 10);
			this.textBox1.Name = "textBox1";
			this.textBox1.Size = new System.Drawing.Size(380, 21);
			this.textBox1.TabIndex = 2;
			this.textBox1.Text = "localhost";
			// 
			// label1
			// 
			this.label1.Location = new System.Drawing.Point(10, 104);
			this.label1.Margin = new System.Windows.Forms.Padding(0, 0, 3, 0);
			this.label1.Name = "label1";
			this.label1.Size = new System.Drawing.Size(67, 21);
			this.label1.TabIndex = 3;
			this.label1.Text = "数据库名称";
			this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// label2
			// 
			this.label2.Location = new System.Drawing.Point(10, 10);
			this.label2.Margin = new System.Windows.Forms.Padding(0, 0, 3, 0);
			this.label2.Name = "label2";
			this.label2.Size = new System.Drawing.Size(67, 21);
			this.label2.TabIndex = 7;
			this.label2.Text = "服务器地址";
			this.label2.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// textBox2
			// 
			this.textBox2.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
									| System.Windows.Forms.AnchorStyles.Right)));
			this.textBox2.Location = new System.Drawing.Point(80, 41);
			this.textBox2.Margin = new System.Windows.Forms.Padding(0, 0, 0, 10);
			this.textBox2.Name = "textBox2";
			this.textBox2.Size = new System.Drawing.Size(380, 21);
			this.textBox2.TabIndex = 6;
			this.textBox2.Text = "root";
			// 
			// label3
			// 
			this.label3.Location = new System.Drawing.Point(10, 41);
			this.label3.Margin = new System.Windows.Forms.Padding(0, 0, 3, 0);
			this.label3.Name = "label3";
			this.label3.Size = new System.Drawing.Size(67, 21);
			this.label3.TabIndex = 9;
			this.label3.Text = "数据库账号";
			this.label3.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// textBox3
			// 
			this.textBox3.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
									| System.Windows.Forms.AnchorStyles.Right)));
			this.textBox3.Location = new System.Drawing.Point(80, 72);
			this.textBox3.Margin = new System.Windows.Forms.Padding(0, 0, 0, 10);
			this.textBox3.Name = "textBox3";
			this.textBox3.Size = new System.Drawing.Size(380, 21);
			this.textBox3.TabIndex = 8;
			this.textBox3.Text = "ybybyb";
			// 
			// textBox4
			// 
			this.textBox4.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
									| System.Windows.Forms.AnchorStyles.Right)));
			this.textBox4.Location = new System.Drawing.Point(80, 103);
			this.textBox4.Margin = new System.Windows.Forms.Padding(0, 0, 0, 10);
			this.textBox4.Name = "textBox4";
			this.textBox4.Size = new System.Drawing.Size(380, 21);
			this.textBox4.TabIndex = 8;
			this.textBox4.Text = "gamedb";
			// 
			// label4
			// 
			this.label4.Location = new System.Drawing.Point(10, 73);
			this.label4.Margin = new System.Windows.Forms.Padding(0, 0, 3, 0);
			this.label4.Name = "label4";
			this.label4.Size = new System.Drawing.Size(67, 21);
			this.label4.TabIndex = 9;
			this.label4.Text = "数据库密码";
			this.label4.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// checkBox1
			// 
			this.checkBox1.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
									| System.Windows.Forms.AnchorStyles.Right)));
			this.checkBox1.Location = new System.Drawing.Point(80, 165);
			this.checkBox1.Margin = new System.Windows.Forms.Padding(0, 0, 10, 10);
			this.checkBox1.Name = "checkBox1";
			this.checkBox1.Size = new System.Drawing.Size(378, 24);
			this.checkBox1.TabIndex = 0;
			this.checkBox1.Text = "包含建库语句（先DROP再CREATE）";
			this.checkBox1.UseVisualStyleBackColor = true;
			// 
			// panel1
			// 
			this.panel1.Controls.Add(this.panel2);
			this.panel1.Controls.Add(this.checkBox1);
			this.panel1.Controls.Add(this.textBox1);
			this.panel1.Controls.Add(this.label4);
			this.panel1.Controls.Add(this.label5);
			this.panel1.Controls.Add(this.label1);
			this.panel1.Controls.Add(this.label3);
			this.panel1.Controls.Add(this.textBox2);
			this.panel1.Controls.Add(this.textBox5);
			this.panel1.Controls.Add(this.textBox4);
			this.panel1.Controls.Add(this.label2);
			this.panel1.Controls.Add(this.textBox3);
			this.panel1.Dock = System.Windows.Forms.DockStyle.Fill;
			this.panel1.Location = new System.Drawing.Point(0, 0);
			this.panel1.Name = "panel1";
			this.panel1.Padding = new System.Windows.Forms.Padding(10);
			this.panel1.Size = new System.Drawing.Size(470, 439);
			this.panel1.TabIndex = 12;
			// 
			// scintilla1
			// 
			this.scintilla1.Dock = System.Windows.Forms.DockStyle.Fill;
			this.scintilla1.Location = new System.Drawing.Point(0, 0);
			this.scintilla1.Margin = new System.Windows.Forms.Padding(0);
			this.scintilla1.Name = "scintilla1";
			this.scintilla1.Size = new System.Drawing.Size(446, 226);
			this.scintilla1.Styles.BraceBad.FontName = "Verdana";
			this.scintilla1.Styles.BraceLight.FontName = "Verdana";
			this.scintilla1.Styles.ControlChar.FontName = "Verdana";
			this.scintilla1.Styles.Default.FontName = "Verdana";
			this.scintilla1.Styles.IndentGuide.FontName = "Verdana";
			this.scintilla1.Styles.LastPredefined.FontName = "Verdana";
			this.scintilla1.Styles.LineNumber.FontName = "Verdana";
			this.scintilla1.Styles.Max.FontName = "Verdana";
			this.scintilla1.TabIndex = 10;
			// 
			// label5
			// 
			this.label5.Location = new System.Drawing.Point(10, 135);
			this.label5.Margin = new System.Windows.Forms.Padding(0, 0, 3, 0);
			this.label5.Name = "label5";
			this.label5.Size = new System.Drawing.Size(67, 21);
			this.label5.TabIndex = 3;
			this.label5.Text = "数据库端口";
			this.label5.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
			// 
			// textBox5
			// 
			this.textBox5.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
									| System.Windows.Forms.AnchorStyles.Right)));
			this.textBox5.Location = new System.Drawing.Point(80, 134);
			this.textBox5.Margin = new System.Windows.Forms.Padding(0, 0, 0, 10);
			this.textBox5.Name = "textBox5";
			this.textBox5.Size = new System.Drawing.Size(380, 21);
			this.textBox5.TabIndex = 8;
			this.textBox5.Text = "3306";
			// 
			// panel2
			// 
			this.panel2.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
									| System.Windows.Forms.AnchorStyles.Left) 
									| System.Windows.Forms.AnchorStyles.Right)));
			this.panel2.BorderStyle = System.Windows.Forms.BorderStyle.Fixed3D;
			this.panel2.Controls.Add(this.scintilla1);
			this.panel2.Location = new System.Drawing.Point(10, 199);
			this.panel2.Margin = new System.Windows.Forms.Padding(0);
			this.panel2.Name = "panel2";
			this.panel2.Size = new System.Drawing.Size(450, 230);
			this.panel2.TabIndex = 11;
			// 
			// DatabaseTab
			// 
			this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 12F);
			this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
			this.Controls.Add(this.panel1);
			this.MinimumSize = new System.Drawing.Size(447, 199);
			this.Name = "DatabaseTab";
			this.Size = new System.Drawing.Size(470, 439);
			this.panel1.ResumeLayout(false);
			this.panel1.PerformLayout();
			((System.ComponentModel.ISupportInitialize)(this.scintilla1)).EndInit();
			this.panel2.ResumeLayout(false);
			this.ResumeLayout(false);
		}
		private System.Windows.Forms.Panel panel2;
		private ScintillaNet.Scintilla scintilla1;
		private System.Windows.Forms.TextBox textBox5;
		private System.Windows.Forms.Label label5;
		private System.Windows.Forms.Panel panel1;
		private System.Windows.Forms.Label label4;
		private System.Windows.Forms.TextBox textBox4;
		private System.Windows.Forms.TextBox textBox3;
		private System.Windows.Forms.Label label3;
		private System.Windows.Forms.TextBox textBox2;
		private System.Windows.Forms.Label label2;
		private System.Windows.Forms.Label label1;
		private System.Windows.Forms.TextBox textBox1;
		private System.Windows.Forms.CheckBox checkBox1;
	}
}
