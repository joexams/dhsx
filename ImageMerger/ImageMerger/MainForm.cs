/*
 * Created by SharpDevelop.
 * User: BG5SBK
 * Date: 2010/9/29
 * Time: 20:38
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.Text;
using System.Collections.Generic;
using System.Text.RegularExpressions;
using System.Drawing;
using System.Drawing.Imaging;
using System.Windows.Forms;

namespace ImageMerger
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
		}
		
		void Button1Click(object sender, EventArgs e)
		{
			if (DialogResult.OK == folderBrowserDialog1.ShowDialog())
			{
				textBox1.Text = folderBrowserDialog1.SelectedPath;
				
				string[] files = Directory.GetFiles(textBox1.Text, "*.png");
				
				Array.Sort<string>(files, delegate(string a, string b){
				    a = Path.GetFileNameWithoutExtension(a);
				    b = Path.GetFileNameWithoutExtension(b);
				    
				    a = numRegex.Match(a).Value;
				    b = numRegex.Match(b).Value;
				    
				    int v1 = 0;
				    int v2 = 0;
				    
				    if (int.TryParse(a, out v1) && int.TryParse(b, out v2))
						return int.Parse(a) - int.Parse(b);
				    else
				    	return 0;
				});
				
				using (Image image = Image.FromFile(files[0]))
				{
					toolStripStatusLabel1.Text = "图片: " + files.Length + " - 宽度: " + image.Width + " - 高度: " + image.Height;
				}
			}
		}
		
		void Button2Click(object sender, EventArgs e)
		{
			saveFileDialog1.Filter = "PNG图片|*.png";
			
			if (DialogResult.OK == saveFileDialog1.ShowDialog())
			{
				textBox2.Text = saveFileDialog1.FileName;
			}
		}
		
		static Regex numRegex = new Regex("\\d+");
		
		void Button3Click(object sender, EventArgs e)
		{
			if (string.IsNullOrEmpty(textBox1.Text) == false && string.IsNullOrEmpty(textBox2.Text) == false)
			{
				try
				{
					int width = 0;
					
					Bitmap output = null;
					
					string[] files = Directory.GetFiles(textBox1.Text, "*.png");
					
					Array.Sort<string>(files, delegate(string a, string b){
					    a = Path.GetFileNameWithoutExtension(a);
					    b = Path.GetFileNameWithoutExtension(b);
					    
					    a = numRegex.Match(a).Value;
					    b = numRegex.Match(b).Value;
					    
					    int v1 = 0;
					    int v2 = 0;
					    
					    if (int.TryParse(a, out v1) && int.TryParse(b, out v2))
							return int.Parse(a) - int.Parse(b);
					    else
					    	return 0;
					});
					
					using (Image image = Image.FromFile(files[0]))
					{
						width = image.Width;
						
						output = new Bitmap(image.Width * files.Length, image.Height);
					}
					
					Graphics g = Graphics.FromImage(output);
					
					for (int i = 0; i < files.Length; i ++)
					{
						using (Image image = Image.FromFile(files[i]))
						{
							g.DrawImage(image, i * width, 0, image.Width, image.Height);
						}
					}
					
					output.Save(textBox2.Text, ImageFormat.Png);
					
					output.Dispose();
					
					MessageBox.Show("合成成功!", "提示", MessageBoxButtons.OK, MessageBoxIcon.Information);
				}
				catch (Exception ex)
				{
					MessageBox.Show("合成失败：" + ex.Message, "提示", MessageBoxButtons.OK, MessageBoxIcon.Error);
				}
			}
		}
		
		void Button4Click(object sender, EventArgs e)
		{
			if (string.IsNullOrEmpty(textBox1.Text) == false && string.IsNullOrEmpty(textBox2.Text) == false)
			{
				try
				{
					string[] files = Directory.GetFiles(textBox1.Text, "*.png");
					
					Array.Sort<string>(files, delegate(string a, string b){
					    a = Path.GetFileNameWithoutExtension(a);
					    b = Path.GetFileNameWithoutExtension(b);
					    
					    a = numRegex.Match(a).Value;
					    b = numRegex.Match(b).Value;
					    
					    int v1 = 0;
					    int v2 = 0;
					    
					    if (int.TryParse(a, out v1) && int.TryParse(b, out v2))
							return int.Parse(a) - int.Parse(b);
					    else
					    	return 0;
					});
					
					int width = 0;
					int height = 0;
					
					foreach (string file in files)
					{
						using (Image image = Image.FromFile(file))
						{
							if (image.Width > width)
								width = image.Width;
							
							height += image.Height;
						}
					}
					
					StringBuilder outputInfo = new StringBuilder();
					
					using(Bitmap output = new Bitmap(width, height))
					{
						using(Graphics g = Graphics.FromImage(output))
						{
							int y = 0;
							
							for (int i = 0; i < files.Length; i ++)
							{
								using (Image image = Image.FromFile(files[i]))
								{
									g.DrawImage(image, 0, y, image.Width, image.Height);
									
									y += image.Height;
									
									outputInfo.AppendFormat(
@"名称：{0}
宽度：{1}px
高度：{2}px

", Path.GetFileNameWithoutExtension(files[i]), image.Width, image.Height);
								}
							}
						}
						
						output.Save(textBox2.Text, ImageFormat.Png);
						
						File.WriteAllText(textBox2.Text + ".txt", outputInfo.ToString());
					}
					
					MessageBox.Show("合成成功!", "提示", MessageBoxButtons.OK, MessageBoxIcon.Information);
				}
				catch (Exception ex)
				{
					MessageBox.Show("合成失败：" + ex.Message, "提示", MessageBoxButtons.OK, MessageBoxIcon.Error);
				}
			}
		}
		
		void Button5Click(object sender, EventArgs e)
		{
			if (Directory.Exists(textBox1.Text))
				System.Diagnostics.Process.Start(textBox1.Text);
			else
				MessageBox.Show("所指定的目录不存在！");
		}
		
		void Button6Click(object sender, EventArgs e)
		{
			if (Directory.Exists(textBox2.Text))
				System.Diagnostics.Process.Start(textBox2.Text);
			else
				MessageBox.Show("所指定的目录不存在！");
		}
	}
}
