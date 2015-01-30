using System;
using System.Text;
using System.Drawing;
using System.Diagnostics;
using System.Drawing.Text;
using System.Windows.Forms;
using System.ComponentModel;
using System.Drawing.Drawing2D;
using System.Collections.Generic;
using System.ComponentModel.Design;
using System.Text.RegularExpressions;
using System.Runtime.InteropServices;
using System.Runtime.CompilerServices;

namespace ProjectHelper
{
	public class MyTextBox : Panel
	{
		//private LineNumbers_For_RichTextBox m_LineNumberBar;
		private MyTextBoxLineNumberBar m_LineNumberBar;
		private MyTextBoxCodeBox m_CodeBox;
		private Panel m_LineNumberBarBorder;
        
		public MyTextBox()
		{
			InitializeLineNumberBar();
		}
		
        private void InitializeLineNumberBar()
        {
        	/*
        	m_LineNumberBar = new LineNumbers_For_RichTextBox();
        	
        	m_LineNumberBar.ParentRichTextBox = this;
        	m_LineNumberBar.Width = 40;
            m_LineNumberBar.Dock = DockStyle.Left;
            m_LineNumberBar.Name = "LineNumberBar";
            m_LineNumberBar.BackColor = Color.FromArgb(0xFF, 0xE4, 0xE4, 0xE4);
            m_LineNumberBar.ForeColor = Color.FromArgb(0xFF, 0x80, 0x80, 0x80);
            
            this.Controls.Add(m_LineNumberBar);
            */
           
            m_LineNumberBar = new MyTextBoxLineNumberBar();
            
            m_LineNumberBar.Width = 40;
            m_LineNumberBar.TabStop = false;
            m_LineNumberBar.Enabled = false;
            m_LineNumberBar.Dock = DockStyle.Left;
            m_LineNumberBar.Name = "LineNumberBar";
            m_LineNumberBar.Cursor = Cursors.Arrow;
            m_LineNumberBar.SelectionProtected = true;
            m_LineNumberBar.BorderStyle = BorderStyle.None;
            m_LineNumberBar.BackColor = Color.FromArgb(0xFF, 0xE4, 0xE4, 0xE4);
            m_LineNumberBar.ForeColor = Color.FromArgb(0xFF, 0x80, 0x80, 0x80);
            m_LineNumberBar.ScrollBars = RichTextBoxScrollBars.None;
            
            m_LineNumberBarBorder = new Panel();
            
            m_LineNumberBarBorder.Width = 46;
            m_LineNumberBarBorder.Enabled = false;
            m_LineNumberBarBorder.Dock = DockStyle.Left;
            m_LineNumberBarBorder.Name = "LineNumberBarBorder";
            m_LineNumberBarBorder.BackColor = Color.FromArgb(0xFF, 0xF0, 0xF0, 0xF0);
            
            m_LineNumberBarBorder.Controls.Add(m_LineNumberBar);
            
            this.Controls.Add(m_LineNumberBarBorder);
            
            m_CodeBox = new MyTextBoxCodeBox(m_LineNumberBar);
            
            m_CodeBox.Left = 48;
        	m_CodeBox.WordWrap = false;
            m_CodeBox.Height = this.Height;
        	m_CodeBox.Width = this.Width - 48;
        	m_CodeBox.BorderStyle = BorderStyle.None;
            m_CodeBox.Anchor = AnchorStyles.Left | AnchorStyles.Top | AnchorStyles.Right | AnchorStyles.Bottom;
            
            this.Controls.Add(m_CodeBox);
            
            this.BackColor = Color.White;
            this.BorderStyle = BorderStyle.Fixed3D;
        }
        
		public override string Text {
			get { return m_CodeBox.Text; }
			set { m_CodeBox.Text = value; }
		}
        
		protected override void OnFontChanged(EventArgs e)
		{
			m_LineNumberBar.Font = this.Font;
			m_CodeBox.Font = this.Font;
			
			base.OnFontChanged(e);
		}
		
		
		public void SqlSyntaxHighlight (string sql)
		{
			m_CodeBox.SqlSyntaxHighlight(sql);
		}
        
        private class MyTextBoxLineNumberBar : RichTextBox
        {
        	public void WndMsgProc(ref Message m)
        	{
        		base.WndProc(ref m);
        	}
        }
        
        private class MyTextBoxCodeBox : RichTextBox
        {
        	public MyTextBoxCodeBox()
        	{
        	}
        	
	        private int _VPrePosition = 0;
	        // ===================================================================  
	        // for NativeWindow and PostMessageA
	        private const int WM_HSCROLL = 0x114;
	        private const int WM_VSCROLL = 0x115;
	        private const int WM_MOUSEWHEEL = 0x20A;
	        private const int WM_COMMAND = 0x111;
	        // ===================================================================  
	        // for GetScroll and PostMessageA
	        private const int WM_USER = 0x400;
	        private const int SBS_HORZ = 0;
	        private const int SBS_VERT = 1;
	        // ===================================================================  
	        // for SubClassing
	        private const int SB_THUMBPOSITION = 4;
	        // ===================================================================  
	        // API Function: GetScrollPos()
	        [DllImport("user32.dll")]
	        private static extern int GetScrollPos(IntPtr hWnd, int nBar);
	        // ===================================================================  
	        // API Function: PostMessageA()
	        [DllImport("user32.dll")]
	        private static extern bool PostMessageA(IntPtr hwnd, int wMsg, int wParam, int lParam);
	        
        	private MyTextBoxLineNumberBar m_LineNumberBar;
        	
        	public MyTextBoxCodeBox(MyTextBoxLineNumberBar lineNumberBar)
        	{
        		m_LineNumberBar = lineNumberBar;
        	}
        	
	        protected override void WndProc(ref Message m)
	        {
	            if (m.Msg == WM_VSCROLL || m.Msg == WM_MOUSEWHEEL)
	            {
	            	Message scrollMsg = Message.Create(m_LineNumberBar.Handle, m.Msg, m.WParam, m.LParam);
	            	
	            	m_LineNumberBar.WndMsgProc(ref scrollMsg);
	            }
	            
	            base.WndProc(ref m);
	        }
        
			protected override void OnVScroll(EventArgs e)
			{
	            int liPosition = GetScrollPos(this.Handle, SBS_VERT);
	            
	            if (liPosition != _VPrePosition)
	            {
	                this._VPrePosition = liPosition;
	                
	                PostMessageA(m_LineNumberBar.Handle, WM_VSCROLL, SB_THUMBPOSITION + 0x10000 * liPosition, 0);
	            }
			}
			
			static Regex sqlSyntax = new Regex(
@"
(?<=^|\s|,|\(|\)|\r|\n)
(
action|asc|auto_increment|
character|char|comment|constraint|collate|create|
datetime|default|delete|drop|
exists|
float|foreign|
engine|
if|index|insert|integer|into|int|
key|
not|no|null|
on|
primary|
references|
set|smallint|
table|tinyint|
unique|update|
values|VARCHAR
)
(?=\s|,|;|$|\(|\)|\r|\n)
|
(\(|\)|,|;|=|`|\.)
|
(?<=[\,\(,\x20,\r,\n,\;]|^)(\d+)(?=[\,\),\x20,\r,\n,\;]|$)
|
('[^']*')
|
(-- [^\r,\n]*)
|
(\r\n)", RegexOptions.Multiline | RegexOptions.IgnoreCase | RegexOptions.IgnorePatternWhitespace);
			
			public void SqlSyntaxHighlight (string sql)
			{
				int lines = 0;
				
				this.Rtf =  
					@"{\rtf1\ansi\ansicpg936\deff0\deflang1033\deflangfe2052" +
					@"{\colortbl ;\red0\green0\blue255;\red0\green0\blue128;\red255\green128\blue0;\red128\green128\blue128;\red0\green128\blue0;}" +
					sqlSyntax.Replace(sql, delegate(Match match){
	                  	if (match.Groups[1].Success)
	                  		return "\\cf1 " + match.Groups[1].Value + "\\cf0 ";
	                  	else if (match.Groups[2].Success)
	                  		return "\\cf2 " + match.Groups[2].Value + "\\cf0 ";
	                  	else if (match.Groups[3].Success)
	                  		return "\\cf3 " + match.Groups[3].Value + "\\cf0 ";
	                  	else if (match.Groups[4].Success)
	                  		return "\\cf4 " + match.Groups[4].Value + "\\cf0 ";
	                  	else if (match.Groups[5].Success)
	                  		return "\\cf5 " + match.Groups[5].Value + "\\cf0 ";
	                  	else if (match.Groups[6].Success) {
	                  		lines += 1;
	                  		return "\\par\r\n";
	                  	}
	                  	
	                  	return match.Value;
					}) + "}";
				
	            StringBuilder lineNumberText = new StringBuilder(lines * 2);
	            
				lineNumberText.Append(@"{\rtf1\qr ");
				
	            for (int i = 1; i <= lines; i++)
	            {
	            	lineNumberText.Append(i.ToString()).AppendLine("\\par");;
	            }
	            
	            lineNumberText.Append("}");
	            
	            m_LineNumberBar.Rtf = lineNumberText.ToString();
			}
        }
	}
	
	#region Temp
	/*
	[DefaultProperty("ParentRichTextBox")]
	public class LineNumbers_For_RichTextBox : Control
	{
	    // Fields
	    private static List<WeakReference> __ENCList = new List<WeakReference>();
	    [AccessedThroughProperty("zParent")]
	    private RichTextBox _zParent;
	    [AccessedThroughProperty("zTimer")]
	    private Timer _zTimer;
	    private bool zAutoSizing;
	    private Size zAutoSizing_Size;
	    private Color zBorderLines_Color;
	    private bool zBorderLines_Show;
	    private DashStyle zBorderLines_Style;
	    private float zBorderLines_Thickness;
	    private Rectangle zContentRectangle;
	    private LineNumberDockSide zDockSide;
	    private LinearGradientMode zGradient_Direction;
	    private Color zGradient_EndColor;
	    private bool zGradient_Show;
	    private Color zGradient_StartColor;
	    private Color zGridLines_Color;
	    private bool zGridLines_Show;
	    private DashStyle zGridLines_Style;
	    private float zGridLines_Thickness;
	    private ContentAlignment zLineNumbers_Alignment;
	    private bool zLineNumbers_AntiAlias;
	    private bool zLineNumbers_ClipByItemRectangle;
	    private string zLineNumbers_Format;
	    private Size zLineNumbers_Offset;
	    private bool zLineNumbers_Show;
	    private bool zLineNumbers_ShowAsHexadecimal;
	    private bool zLineNumbers_ShowLeadingZeroes;
	    private List<LineNumberItem> zLNIs;
	    private Color zMarginLines_Color;
	    private bool zMarginLines_Show;
	    private LineNumberDockSide zMarginLines_Side;
	    private DashStyle zMarginLines_Style;
	    private float zMarginLines_Thickness;
	    private int zParentInMe;
	    private bool zParentIsScrolling;
	    private Point zPointInMe;
	    private Point zPointInParent;
	    private bool zSeeThroughMode;
	
	    // Methods
	    public LineNumbers_For_RichTextBox()
	    {
	        __ENCAddToList(this);
	        this.zParent = null;
	        this.zTimer = new Timer();
	        this.zAutoSizing = true;
	        this.zAutoSizing_Size = new Size(0, 0);
	        this.zContentRectangle = new Rectangle();
	        this.zDockSide = LineNumberDockSide.Left;
	        this.zParentIsScrolling = false;
	        this.zSeeThroughMode = false;
	        this.zGradient_Show = true;
	        this.zGradient_Direction = LinearGradientMode.Horizontal;
	        this.zGradient_StartColor = Color.FromArgb(0, 0, 0, 0);
	        this.zGradient_EndColor = Color.LightSteelBlue;
	        this.zGridLines_Show = true;
	        this.zGridLines_Thickness = 1f;
	        this.zGridLines_Style = DashStyle.Dot;
	        this.zGridLines_Color = Color.SlateGray;
	        this.zBorderLines_Show = true;
	        this.zBorderLines_Thickness = 1f;
	        this.zBorderLines_Style = DashStyle.Dot;
	        this.zBorderLines_Color = Color.SlateGray;
	        this.zMarginLines_Show = true;
	        this.zMarginLines_Side = LineNumberDockSide.Right;
	        this.zMarginLines_Thickness = 1f;
	        this.zMarginLines_Style = DashStyle.Solid;
	        this.zMarginLines_Color = Color.SlateGray;
	        this.zLineNumbers_Show = true;
	        this.zLineNumbers_ShowLeadingZeroes = true;
	        this.zLineNumbers_ShowAsHexadecimal = false;
	        this.zLineNumbers_ClipByItemRectangle = true;
	        this.zLineNumbers_Offset = new Size(0, 0);
	        this.zLineNumbers_Format = "0";
	        this.zLineNumbers_Alignment = ContentAlignment.TopRight;
	        this.zLineNumbers_AntiAlias = true;
	        this.zLNIs = new List<LineNumberItem>();
	        this.zPointInParent = new Point(0, 0);
	        this.zPointInMe = new Point(0, 0);
	        this.zParentInMe = 0;
	        LineNumbers_For_RichTextBox VB_t_ref_L0 = this;
	        VB_t_ref_L0.SetStyle(ControlStyles.OptimizedDoubleBuffer, true);
	        VB_t_ref_L0.SetStyle(ControlStyles.ResizeRedraw, true);
	        VB_t_ref_L0.SetStyle(ControlStyles.SupportsTransparentBackColor, true);
	        VB_t_ref_L0.SetStyle(ControlStyles.UserPaint, true);
	        VB_t_ref_L0.SetStyle(ControlStyles.AllPaintingInWmPaint, true);
	        Padding VB_t_struct_S2 = new Padding(0);
	        VB_t_ref_L0.Margin = VB_t_struct_S2;
	        VB_t_struct_S2 = new Padding(0, 0, 2, 0);
	        VB_t_ref_L0.Padding = VB_t_struct_S2;
	        VB_t_ref_L0 = null;
	        Timer VB_t_ref_L1 = this.zTimer;
	        VB_t_ref_L1.Enabled = true;
	        VB_t_ref_L1.Interval = 200;
	        VB_t_ref_L1.Stop();
	        VB_t_ref_L1 = null;
	        this.Update_SizeAndPosition();
	        this.Invalidate();
	    }
	
	    [DebuggerNonUserCode]
	    private static void __ENCAddToList(object value)
	    {
	        List<WeakReference> list = __ENCList;
	        lock (list)
	        {
	            if (__ENCList.Count == __ENCList.Capacity)
	            {
	                int index = 0;
	                int num3 = __ENCList.Count - 1;
	                for (int i = 0; i <= num3; i++)
	                {
	                    WeakReference reference = __ENCList[i];
	                    if (reference.IsAlive)
	                    {
	                        if (i != index)
	                        {
	                            __ENCList[index] = __ENCList[i];
	                        }
	                        index++;
	                    }
	                }
	                __ENCList.RemoveRange(index, __ENCList.Count - index);
	                __ENCList.Capacity = __ENCList.Count;
	            }
	            __ENCList.Add(new WeakReference(RuntimeHelpers.GetObjectValue(value)));
	        }
	    }
	
	    private void FindStartIndex(ref int zMin, ref int zMax, ref int zTarget)
	    {
	        if (!((zMax == (zMin + 1)) | (zMin == ((zMax + zMin) / 2))))
	        {
	            int VB_t_i4_L0 = this.zParent.GetPositionFromCharIndex((zMax + zMin) / 2).Y;
	            if (VB_t_i4_L0 == zTarget)
	            {
	                zMin = (zMax + zMin) / 2;
	            }
	            else if (VB_t_i4_L0 > zTarget)
	            {
	                zMax = (zMax + zMin) / 2;
	                this.FindStartIndex(ref zMin, ref zMax, ref zTarget);
	            }
	            else if (VB_t_i4_L0 < 0)
	            {
	                zMin = (zMax + zMin) / 2;
	                this.FindStartIndex(ref zMin, ref zMax, ref zTarget);
	            }
	        }
	    }
	
	    protected override void OnHandleCreated(EventArgs e)
	    {
	        base.OnHandleCreated(e);
	        this.AutoSize = false;
	    }
	
	    protected override void OnLocationChanged(EventArgs e)
	    {
	        if (this.DesignMode)
	        {
	            this.Refresh();
	        }
	        base.OnLocationChanged(e);
	        this.Invalidate();
	    }
	
	    protected override void OnPaint(PaintEventArgs e)
	    {
	        SizeF zTextSize;
	        Point VB_t_struct_S1;
	        Point VB_t_struct_S2;
	        Rectangle VB_t_struct_S3;
	        this.Update_VisibleLineNumberItems();
	        base.OnPaint(e);
	        if (this.zLineNumbers_AntiAlias)
	        {
	            e.Graphics.TextRenderingHint = TextRenderingHint.AntiAlias;
	        }
	        else
	        {
	            e.Graphics.TextRenderingHint = TextRenderingHint.SystemDefault;
	        }
	        string zTextToShow = "";
	        string zReminderToShow = "";
	        StringFormat zSF = new StringFormat();
	        Pen zPen = new Pen(this.ForeColor);
	        SolidBrush zBrush = new SolidBrush(this.ForeColor);
	        Point zPoint = new Point(0, 0);
	        Rectangle zItemClipRectangle = new Rectangle(0, 0, 0, 0);
	        GraphicsPath zGP_GridLines = new GraphicsPath(FillMode.Winding);
	        GraphicsPath zGP_BorderLines = new GraphicsPath(FillMode.Winding);
	        GraphicsPath zGP_MarginLines = new GraphicsPath(FillMode.Winding);
	        GraphicsPath zGP_LineNumbers = new GraphicsPath(FillMode.Winding);
	        Region zRegion = new Region(base.ClientRectangle);
	        if (this.DesignMode)
	        {
	            if (this.zParent == null)
	            {
	                zReminderToShow = "-!- Set ParentRichTextBox -!-";
	            }
	            else if (this.zLNIs.Count == 0)
	            {
	                zReminderToShow = "LineNrs (  " + this.zParent.Name + "  )";
	            }
	            if (zReminderToShow.Length > 0)
	            {
	                e.Graphics.TranslateTransform((float) (((double) this.Width) / 2.0), (float) (((double) this.Height) / 2.0));
	                e.Graphics.RotateTransform(-90f);
	                zSF.Alignment = StringAlignment.Center;
	                zSF.LineAlignment = StringAlignment.Center;
	                zTextSize = e.Graphics.MeasureString(zReminderToShow, this.Font, (PointF) zPoint, zSF);
	                e.Graphics.DrawString(zReminderToShow, this.Font, Brushes.WhiteSmoke, 1f, 1f, zSF);
	                e.Graphics.DrawString(zReminderToShow, this.Font, Brushes.Firebrick, 0f, 0f, zSF);
	                e.Graphics.ResetTransform();
	                Rectangle zReminderRectangle = new Rectangle((int) Math.Round((double) ((((double) this.Width) / 2.0) - (zTextSize.Height / 2f))), (int) Math.Round((double) ((((double) this.Height) / 2.0) - (zTextSize.Width / 2f))), (int) Math.Round((double) zTextSize.Height), (int) Math.Round((double) zTextSize.Width));
	                zGP_LineNumbers.AddRectangle(zReminderRectangle);
	                zGP_LineNumbers.CloseFigure();
	                if (this.zAutoSizing)
	                {
	                    zReminderRectangle.Inflate((int) Math.Round((double) (zTextSize.Height * 0.2)), (int) Math.Round((double) (zTextSize.Width * 0.1)));
	                    this.zAutoSizing_Size = new Size(zReminderRectangle.Width, zReminderRectangle.Height);
	                }
	            }
	        }
	        if (this.zLNIs.Count > 0)
	        {
	            LinearGradientBrush zLGB = null;
	            zPen = new Pen(this.zGridLines_Color, this.zGridLines_Thickness);
	            zPen.DashStyle = this.zGridLines_Style;
	            zSF.Alignment = StringAlignment.Near;
	            zSF.LineAlignment = StringAlignment.Near;
	            zSF.FormatFlags = StringFormatFlags.NoClip | StringFormatFlags.NoWrap | StringFormatFlags.FitBlackBox;
	            int VB_t_i4_L0 = this.zLNIs.Count - 1;
	            for (int zA = 0; zA <= VB_t_i4_L0; zA++)
	            {
	                if (this.zGradient_Show)
	                {
	                    zLGB = new LinearGradientBrush(this.zLNIs[zA].Rectangle, this.zGradient_StartColor, this.zGradient_EndColor, this.zGradient_Direction);
	                    e.Graphics.FillRectangle(zLGB, this.zLNIs[zA].Rectangle);
	                }
	                if (this.zGridLines_Show)
	                {
	                    VB_t_struct_S1 = new Point(0, this.zLNIs[zA].Rectangle.Y);
	                    VB_t_struct_S2 = new Point(this.Width, this.zLNIs[zA].Rectangle.Y);
	                    e.Graphics.DrawLine(zPen, VB_t_struct_S1, VB_t_struct_S2);
	                    VB_t_struct_S3 = new Rectangle((int) Math.Round((double) -this.zGridLines_Thickness), this.zLNIs[zA].Rectangle.Y, (int) Math.Round((double) (this.Width + (this.zGridLines_Thickness * 2f))), (int) Math.Round((double) ((this.Height - this.zLNIs[0].Rectangle.Y) + this.zGridLines_Thickness)));
	                    zGP_GridLines.AddRectangle(VB_t_struct_S3);
	                    zGP_GridLines.CloseFigure();
	                }
	                if (this.zLineNumbers_Show)
	                {
	                    if (this.zLineNumbers_ShowLeadingZeroes)
	                    {
	                    	zTextToShow = this.zLineNumbers_ShowAsHexadecimal ? this.zLNIs[zA].LineNumber.ToString("X") : this.zLNIs[zA].LineNumber.ToString(this.zLineNumbers_Format);
	                    }
	                    else
	                    {
	                    	zTextToShow = this.zLineNumbers_ShowAsHexadecimal ? this.zLNIs[zA].LineNumber.ToString("X") : this.zLNIs[zA].LineNumber.ToString();
	                    }
	                    zTextSize = e.Graphics.MeasureString(zTextToShow, this.Font, (PointF) zPoint, zSF);
	                    switch (this.zLineNumbers_Alignment)
	                    {
	                        case ContentAlignment.TopLeft:
	                            zPoint = new Point((this.zLNIs[zA].Rectangle.Left + this.Padding.Left) + this.zLineNumbers_Offset.Width, (this.zLNIs[zA].Rectangle.Top + this.Padding.Top) + this.zLineNumbers_Offset.Height);
	                            break;
	
	                        case ContentAlignment.MiddleLeft:
	                            zPoint = new Point((this.zLNIs[zA].Rectangle.Left + this.Padding.Left) + this.zLineNumbers_Offset.Width, (int) Math.Round((double) (((this.zLNIs[zA].Rectangle.Top + (((double) this.zLNIs[zA].Rectangle.Height) / 2.0)) + this.zLineNumbers_Offset.Height) - (zTextSize.Height / 2f))));
	                            break;
	
	                        case ContentAlignment.BottomLeft:
	                            zPoint = new Point((this.zLNIs[zA].Rectangle.Left + this.Padding.Left) + this.zLineNumbers_Offset.Width, (int) Math.Round((double) ((((this.zLNIs[zA].Rectangle.Bottom - this.Padding.Bottom) + 1) + this.zLineNumbers_Offset.Height) - zTextSize.Height)));
	                            break;
	
	                        case ContentAlignment.TopCenter:
	                            zPoint = new Point((int) Math.Round((double) (((((double) this.zLNIs[zA].Rectangle.Width) / 2.0) + this.zLineNumbers_Offset.Width) - (zTextSize.Width / 2f))), (this.zLNIs[zA].Rectangle.Top + this.Padding.Top) + this.zLineNumbers_Offset.Height);
	                            break;
	
	                        case ContentAlignment.MiddleCenter:
	                            zPoint = new Point((int) Math.Round((double) (((((double) this.zLNIs[zA].Rectangle.Width) / 2.0) + this.zLineNumbers_Offset.Width) - (zTextSize.Width / 2f))), (int) Math.Round((double) (((this.zLNIs[zA].Rectangle.Top + (((double) this.zLNIs[zA].Rectangle.Height) / 2.0)) + this.zLineNumbers_Offset.Height) - (zTextSize.Height / 2f))));
	                            break;
	
	                        case ContentAlignment.BottomCenter:
	                            zPoint = new Point((int) Math.Round((double) (((((double) this.zLNIs[zA].Rectangle.Width) / 2.0) + this.zLineNumbers_Offset.Width) - (zTextSize.Width / 2f))), (int) Math.Round((double) ((((this.zLNIs[zA].Rectangle.Bottom - this.Padding.Bottom) + 1) + this.zLineNumbers_Offset.Height) - zTextSize.Height)));
	                            break;
	
	                        case ContentAlignment.TopRight:
	                            zPoint = new Point((int) Math.Round((double) (((this.zLNIs[zA].Rectangle.Right - this.Padding.Right) + this.zLineNumbers_Offset.Width) - zTextSize.Width)), (this.zLNIs[zA].Rectangle.Top + this.Padding.Top) + this.zLineNumbers_Offset.Height);
	                            break;
	
	                        case ContentAlignment.MiddleRight:
	                            zPoint = new Point((int) Math.Round((double) (((this.zLNIs[zA].Rectangle.Right - this.Padding.Right) + this.zLineNumbers_Offset.Width) - zTextSize.Width)), (int) Math.Round((double) (((this.zLNIs[zA].Rectangle.Top + (((double) this.zLNIs[zA].Rectangle.Height) / 2.0)) + this.zLineNumbers_Offset.Height) - (zTextSize.Height / 2f))));
	                            break;
	
	                        case ContentAlignment.BottomRight:
	                            zPoint = new Point((int) Math.Round((double) (((this.zLNIs[zA].Rectangle.Right - this.Padding.Right) + this.zLineNumbers_Offset.Width) - zTextSize.Width)), (int) Math.Round((double) ((((this.zLNIs[zA].Rectangle.Bottom - this.Padding.Bottom) + 1) + this.zLineNumbers_Offset.Height) - zTextSize.Height)));
	                            break;
	                    }
	                    zItemClipRectangle = new Rectangle(zPoint, zTextSize.ToSize());
	                    if (this.zLineNumbers_ClipByItemRectangle)
	                    {
	                        zItemClipRectangle.Intersect(this.zLNIs[zA].Rectangle);
	                        e.Graphics.SetClip(zItemClipRectangle);
	                    }
	                    e.Graphics.DrawString(zTextToShow, this.Font, zBrush, (PointF) zPoint, zSF);
	                    e.Graphics.ResetClip();
	                    zGP_LineNumbers.AddRectangle(zItemClipRectangle);
	                    zGP_LineNumbers.CloseFigure();
	                }
	            }
	            if (this.zGridLines_Show)
	            {
	                zPen.DashStyle = DashStyle.Solid;
	                zGP_GridLines.Widen(zPen);
	            }
	            if (zLGB != null)
	            {
	                zLGB.Dispose();
	            }
	        }
	        Point zP_Left = new Point((int) Math.Round(Math.Floor((double) (this.zBorderLines_Thickness / 2f))), (int) Math.Round(Math.Floor((double) (this.zBorderLines_Thickness / 2f))));
	        Point zP_Right = new Point((int) Math.Round((double) (this.Width - Math.Ceiling((double) (this.zBorderLines_Thickness / 2f)))), (int) Math.Round((double) (this.Height - Math.Ceiling((double) (this.zBorderLines_Thickness / 2f)))));
	        Point[] VB_LW_t_array_S0 = new Point[5];
	        VB_t_struct_S2 = new Point(zP_Left.X, zP_Left.Y);
	        VB_LW_t_array_S0[0] = VB_t_struct_S2;
	        VB_t_struct_S1 = new Point(zP_Right.X, zP_Left.Y);
	        VB_LW_t_array_S0[1] = VB_t_struct_S1;
	        Point VB_t_struct_S6 = new Point(zP_Right.X, zP_Right.Y);
	        VB_LW_t_array_S0[2] = VB_t_struct_S6;
	        Point VB_t_struct_S7 = new Point(zP_Left.X, zP_Right.Y);
	        VB_LW_t_array_S0[3] = VB_t_struct_S7;
	        Point VB_t_struct_S8 = new Point(zP_Left.X, zP_Left.Y);
	        VB_LW_t_array_S0[4] = VB_t_struct_S8;
	        Point[] zBorderLines_Points = VB_LW_t_array_S0;
	        if (this.zBorderLines_Show)
	        {
	            zPen = new Pen(this.zBorderLines_Color, this.zBorderLines_Thickness);
	            zPen.DashStyle = this.zBorderLines_Style;
	            e.Graphics.DrawLines(zPen, zBorderLines_Points);
	            zGP_BorderLines.AddLines(zBorderLines_Points);
	            zGP_BorderLines.CloseFigure();
	            zPen.DashStyle = DashStyle.Solid;
	            zGP_BorderLines.Widen(zPen);
	        }
	        if (this.zMarginLines_Show && (this.zMarginLines_Side > LineNumberDockSide.None))
	        {
	            zP_Left = new Point((int) Math.Round((double) -this.zMarginLines_Thickness), (int) Math.Round((double) -this.zMarginLines_Thickness));
	            zP_Right = new Point((int) Math.Round((double) (this.Width + this.zMarginLines_Thickness)), (int) Math.Round((double) (this.Height + this.zMarginLines_Thickness)));
	            zPen = new Pen(this.zMarginLines_Color, this.zMarginLines_Thickness);
	            zPen.DashStyle = this.zMarginLines_Style;
	            if ((this.zMarginLines_Side == LineNumberDockSide.Left) | (this.zMarginLines_Side == LineNumberDockSide.Height))
	            {
	                VB_t_struct_S8 = new Point((int) Math.Round(Math.Floor((double) (this.zMarginLines_Thickness / 2f))), 0);
	                VB_t_struct_S7 = new Point((int) Math.Round(Math.Floor((double) (this.zMarginLines_Thickness / 2f))), this.Height - 1);
	                e.Graphics.DrawLine(zPen, VB_t_struct_S8, VB_t_struct_S7);
	                zP_Left = new Point((int) Math.Round(Math.Ceiling((double) (this.zMarginLines_Thickness / 2f))), (int) Math.Round((double) -this.zMarginLines_Thickness));
	            }
	            if ((this.zMarginLines_Side == LineNumberDockSide.Right) | (this.zMarginLines_Side == LineNumberDockSide.Height))
	            {
	                VB_t_struct_S8 = new Point((int) Math.Round((double) (this.Width - Math.Ceiling((double) (this.zMarginLines_Thickness / 2f)))), 0);
	                VB_t_struct_S7 = new Point((int) Math.Round((double) (this.Width - Math.Ceiling((double) (this.zMarginLines_Thickness / 2f)))), this.Height - 1);
	                e.Graphics.DrawLine(zPen, VB_t_struct_S8, VB_t_struct_S7);
	                zP_Right = new Point((int) Math.Round((double) (this.Width - Math.Ceiling((double) (this.zMarginLines_Thickness / 2f)))), (int) Math.Round((double) (this.Height + this.zMarginLines_Thickness)));
	            }
	            Size VB_t_struct_S0 = new Size(zP_Right.X - zP_Left.X, zP_Right.Y - zP_Left.Y);
	            VB_t_struct_S3 = new Rectangle(zP_Left, VB_t_struct_S0);
	            zGP_MarginLines.AddRectangle(VB_t_struct_S3);
	            zPen.DashStyle = DashStyle.Solid;
	            zGP_MarginLines.Widen(zPen);
	        }
	        if (this.zSeeThroughMode)
	        {
	            zRegion.MakeEmpty();
	            zRegion.Union(zGP_BorderLines);
	            zRegion.Union(zGP_MarginLines);
	            zRegion.Union(zGP_GridLines);
	            zRegion.Union(zGP_LineNumbers);
	        }
	        if (zRegion.GetBounds(e.Graphics).IsEmpty)
	        {
	            zGP_BorderLines.AddLines(zBorderLines_Points);
	            zGP_BorderLines.CloseFigure();
	            zPen = new Pen(this.zBorderLines_Color, 1f);
	            zPen.DashStyle = DashStyle.Solid;
	            zGP_BorderLines.Widen(zPen);
	            zRegion = new Region(zGP_BorderLines);
	        }
	        this.Region = zRegion;
	        if (zPen != null)
	        {
	            zPen.Dispose();
	        }
	        if (zBrush != null)
	        {
	            zPen.Dispose();
	        }
	        if (zRegion != null)
	        {
	            zRegion.Dispose();
	        }
	        if (zGP_GridLines != null)
	        {
	            zGP_GridLines.Dispose();
	        }
	        if (zGP_BorderLines != null)
	        {
	            zGP_BorderLines.Dispose();
	        }
	        if (zGP_MarginLines != null)
	        {
	            zGP_MarginLines.Dispose();
	        }
	        if (zGP_LineNumbers != null)
	        {
	            zGP_LineNumbers.Dispose();
	        }
	    }
	
	    protected override void OnSizeChanged(EventArgs e)
	    {
	        if (this.DesignMode)
	        {
	            this.Refresh();
	        }
	        base.OnSizeChanged(e);
	        this.Invalidate();
	    }
	
	    public override void Refresh()
	    {
	        base.Refresh();
	        this.Update_SizeAndPosition();
	    }
	
	    private void Update_SizeAndPosition()
	    {
	        if (!this.AutoSize && !(((this.Dock == DockStyle.Bottom) | (this.Dock == DockStyle.Fill)) | (this.Dock == DockStyle.Top)))
	        {
	            Point zNewLocation = this.Location;
	            Size zNewSize = this.Size;
	            if (this.zAutoSizing)
	            {
	                bool VB_t_bool_L0 = true;
	                if (VB_t_bool_L0 == (this.zParent == null))
	                {
	                    if (this.zAutoSizing_Size.Width > 0)
	                    {
	                        zNewSize.Width = this.zAutoSizing_Size.Width;
	                    }
	                    if (this.zAutoSizing_Size.Height > 0)
	                    {
	                        zNewSize.Height = this.zAutoSizing_Size.Height;
	                    }
	                    this.Size = zNewSize;
	                }
	                else if (VB_t_bool_L0 == ((this.Dock == DockStyle.Left) | (this.Dock == DockStyle.Right)))
	                {
	                    if (this.zAutoSizing_Size.Width > 0)
	                    {
	                        zNewSize.Width = this.zAutoSizing_Size.Width;
	                    }
	                    this.Width = zNewSize.Width;
	                }
	                else if (VB_t_bool_L0 == (this.zDockSide != LineNumberDockSide.None))
	                {
	                    if (this.zAutoSizing_Size.Width > 0)
	                    {
	                        zNewSize.Width = this.zAutoSizing_Size.Width;
	                    }
	                    zNewSize.Height = this.zParent.Height;
	                    if (this.zDockSide == LineNumberDockSide.Left)
	                    {
	                        zNewLocation.X = (this.zParent.Left - zNewSize.Width) - 1;
	                    }
	                    if (this.zDockSide == LineNumberDockSide.Right)
	                    {
	                        zNewLocation.X = this.zParent.Right + 1;
	                    }
	                    zNewLocation.Y = this.zParent.Top;
	                    this.Location = zNewLocation;
	                    this.Size = zNewSize;
	                }
	                else if (VB_t_bool_L0 == (this.zDockSide == LineNumberDockSide.None))
	                {
	                    if (this.zAutoSizing_Size.Width > 0)
	                    {
	                        zNewSize.Width = this.zAutoSizing_Size.Width;
	                    }
	                    this.Size = zNewSize;
	                }
	            }
	            else
	            {
	                bool VB_t_bool_L1 = true;
	                if (VB_t_bool_L1 == (this.zParent == null))
	                {
	                    if (this.zAutoSizing_Size.Width > 0)
	                    {
	                        zNewSize.Width = this.zAutoSizing_Size.Width;
	                    }
	                    if (this.zAutoSizing_Size.Height > 0)
	                    {
	                        zNewSize.Height = this.zAutoSizing_Size.Height;
	                    }
	                    this.Size = zNewSize;
	                }
	                else if (VB_t_bool_L1 == (this.zDockSide != LineNumberDockSide.None))
	                {
	                    zNewSize.Height = this.zParent.Height;
	                    if (this.zDockSide == LineNumberDockSide.Left)
	                    {
	                        zNewLocation.X = (this.zParent.Left - zNewSize.Width) - 1;
	                    }
	                    if (this.zDockSide == LineNumberDockSide.Right)
	                    {
	                        zNewLocation.X = this.zParent.Right + 1;
	                    }
	                    zNewLocation.Y = this.zParent.Top;
	                    this.Location = zNewLocation;
	                    this.Size = zNewSize;
	                }
	            }
	        }
	    }
	
	    private void Update_VisibleLineNumberItems()
	    {
	        this.zLNIs.Clear();
	        this.zAutoSizing_Size = new Size(0, 0);
	        this.zLineNumbers_Format = "0";
	        if (this.zAutoSizing)
	        {
	            this.zAutoSizing_Size = new Size(TextRenderer.MeasureText(this.zLineNumbers_Format.Replace(new string("0".ToCharArray()), new string("W".ToCharArray())), this.Font).Width, 0);
	        }
	        if ((this.zParent != null) && (this.zParent.Text != ""))
	        {
	            Rectangle VB_t_struct_S2 = this.zParent.ClientRectangle;
	            this.zPointInParent = this.zParent.PointToScreen(VB_t_struct_S2.Location);
	            Point VB_t_struct_S3 = new Point(0, 0);
	            this.zPointInMe = this.PointToScreen(VB_t_struct_S3);
	            this.zParentInMe = (this.zPointInParent.Y - this.zPointInMe.Y) + 1;
	            this.zPointInParent = this.zParent.PointToClient(this.zPointInMe);
	            string[] zSplit = this.zParent.Text.Split("\r\n".ToCharArray());
	            if (zSplit.Length < 2)
	            {
	                Point zPoint = this.zParent.GetPositionFromCharIndex(0);
	                VB_t_struct_S3 = new Point(0, (zPoint.Y - 1) + this.zParentInMe);
	                Size VB_t_struct_S1 = new Size(this.Width, this.zContentRectangle.Height - zPoint.Y);
	                VB_t_struct_S2 = new Rectangle(VB_t_struct_S3, VB_t_struct_S1);
	                this.zLNIs.Add(new LineNumberItem(1, VB_t_struct_S2));
	            }
	            else
	            {
	                TimeSpan zTimeSpan = new TimeSpan(DateTime.Now.Ticks);
	                Point zPoint = new Point(0, 0);
	                int zStartIndex = 0;
	                int zA = this.zParent.Text.Length - 1;
	                int VB_t_i4_S0 = this.zPointInParent.Y;
	                this.FindStartIndex(ref zStartIndex, ref zA, ref VB_t_i4_S0);
	                this.zPointInParent.Y = VB_t_i4_S0;
	                zStartIndex = Math.Max(0, Math.Min((int) (this.zParent.Text.Length - 1), (int) (this.zParent.Text.Substring(0, zStartIndex).LastIndexOf('\n') + 1)));
	                int VB_t_i4_L1 = zSplit.Length - 1;
	                zA = Math.Max(0, this.zParent.Text.Substring(0, zStartIndex).Split("\r\n".ToCharArray()).Length - 1);
	                while (zA <= VB_t_i4_L1)
	                {
	                    zPoint = this.zParent.GetPositionFromCharIndex(zStartIndex);
	                    zStartIndex += Math.Max(1, zSplit[zA].Length + 1);
	                    if ((zPoint.Y + this.zParentInMe) > this.Height)
	                    {
	                        break;
	                    }
	                    VB_t_struct_S2 = new Rectangle(0, (zPoint.Y - 1) + this.zParentInMe, this.Width, 1);
	                    this.zLNIs.Add(new LineNumberItem(zA + 1, VB_t_struct_S2));
	                    if (this.zParentIsScrolling && (DateTime.Now.Ticks > (zTimeSpan.Ticks + 500000L)))
	                    {
	                        if (this.zLNIs.Count == 1)
	                        {
	                            this.zLNIs[0].Rectangle.Y = 0;
	                        }
	                        this.zParentIsScrolling = false;
	                        this.zTimer.Start();
	                        break;
	                    }
	                    zA++;
	                }
	                if (this.zLNIs.Count == 0)
	                {
	                    return;
	                }
	                if (zA < zSplit.Length)
	                {
	                    zPoint = this.zParent.GetPositionFromCharIndex(Math.Min(zStartIndex, this.zParent.Text.Length - 1));
	                    VB_t_struct_S2 = new Rectangle(0, (zPoint.Y - 1) + this.zParentInMe, 0, 0);
	                    this.zLNIs.Add(new LineNumberItem(-1, VB_t_struct_S2));
	                }
	                else
	                {
	                    VB_t_struct_S2 = new Rectangle(0, this.zContentRectangle.Bottom, 0, 0);
	                    this.zLNIs.Add(new LineNumberItem(-1, VB_t_struct_S2));
	                }
	                int VB_t_i4_L2 = this.zLNIs.Count - 2;
	                for (zA = 0; zA <= VB_t_i4_L2; zA++)
	                {
	                    this.zLNIs[zA].Rectangle.Height = Math.Max(1, this.zLNIs[zA + 1].Rectangle.Y - this.zLNIs[zA].Rectangle.Y);
	                }
	                this.zLNIs.RemoveAt(this.zLNIs.Count - 1);
	                if (this.zLineNumbers_ShowAsHexadecimal)
	                {
	                    VB_t_i4_S0 = zSplit.Length;
	                    this.zLineNumbers_Format = "".PadRight(VB_t_i4_S0.ToString("X").Length, '0');
	                }
	                else
	                {
	                    VB_t_i4_S0 = zSplit.Length;
	                    this.zLineNumbers_Format = "".PadRight(VB_t_i4_S0.ToString().Length, '0');
	                }
	            }
	            if (this.zAutoSizing)
	            {
	                this.zAutoSizing_Size = new Size(TextRenderer.MeasureText(this.zLineNumbers_Format.Replace(new string("0".ToCharArray()), new string("W".ToCharArray())), this.Font).Width, 0);
	            }
	        }
	    }
	
	    private void zParent_Changed(object sender, EventArgs e)
	    {
	        this.Refresh();
	        this.Invalidate();
	    }
	
	    private void zParent_ContentsResized(object sender, ContentsResizedEventArgs e)
	    {
	        this.zContentRectangle = e.NewRectangle;
	        this.Refresh();
	        this.Invalidate();
	    }
	
	    private void zParent_Disposed(object sender, EventArgs e)
	    {
	        this.ParentRichTextBox = null;
	        this.Refresh();
	        this.Invalidate();
	    }
	
	    private void zParent_Scroll(object sender, EventArgs e)
	    {
	        this.zParentIsScrolling = true;
	        this.Invalidate();
	    }
	
	    private void zTimer_Tick(object sender, EventArgs e)
	    {
	        this.zParentIsScrolling = false;
	        this.zTimer.Stop();
	        this.Invalidate();
	    }
	
	    // Properties
	    [Category("Additional Behavior"), Description("Use this property to enable the control to act as an overlay ontop of the RichTextBox.")]
	    public bool _SeeThroughMode_
	    {
	        get
	        {
	            return this.zSeeThroughMode;
	        }
	        set
	        {
	            this.zSeeThroughMode = value;
	            this.Invalidate();
	        }
	    }
	
	    [Browsable(false)]
	    public override bool AutoSize
	    {
	        get
	        {
	            return base.AutoSize;
	        }
	        set
	        {
	            base.AutoSize = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Behavior"), Description("Use this property to automatically resize the control (and reposition it if needed).")]
	    public bool AutoSizing
	    {
	        get
	        {
	            return this.zAutoSizing;
	        }
	        set
	        {
	            this.zAutoSizing = value;
	            this.Refresh();
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public Color BackgroundGradient_AlphaColor
	    {
	        get
	        {
	            return this.zGradient_StartColor;
	        }
	        set
	        {
	            this.zGradient_StartColor = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public Color BackgroundGradient_BetaColor
	    {
	        get
	        {
	            return this.zGradient_EndColor;
	        }
	        set
	        {
	            this.zGradient_EndColor = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public LinearGradientMode BackgroundGradient_Direction
	    {
	        get
	        {
	            return this.zGradient_Direction;
	        }
	        set
	        {
	            this.zGradient_Direction = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public Color BorderLines_Color
	    {
	        get
	        {
	            return this.zBorderLines_Color;
	        }
	        set
	        {
	            this.zBorderLines_Color = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public DashStyle BorderLines_Style
	    {
	        get
	        {
	            return this.zBorderLines_Style;
	        }
	        set
	        {
	            if (value == DashStyle.Custom)
	            {
	                value = DashStyle.Solid;
	            }
	            this.zBorderLines_Style = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public float BorderLines_Thickness
	    {
	        get
	        {
	            return this.zBorderLines_Thickness;
	        }
	        set
	        {
	            this.zBorderLines_Thickness = Math.Max(1f, Math.Min(255f, value));
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Behavior"), Description("Use this property to dock the LineNumbers to a chosen side of the chosen RichTextBox.")]
	    public LineNumberDockSide DockSide
	    {
	        get
	        {
	            return this.zDockSide;
	        }
	        set
	        {
	            this.zDockSide = value;
	            this.Refresh();
	            this.Invalidate();
	        }
	    }
	
	    [Browsable(true)]
	    public override Font Font
	    {
	        get
	        {
	            return base.Font;
	        }
	        set
	        {
	            base.Font = value;
	            this.Refresh();
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public Color GridLines_Color
	    {
	        get
	        {
	            return this.zGridLines_Color;
	        }
	        set
	        {
	            this.zGridLines_Color = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public DashStyle GridLines_Style
	    {
	        get
	        {
	            return this.zGridLines_Style;
	        }
	        set
	        {
	            if (value == DashStyle.Custom)
	            {
	                value = DashStyle.Solid;
	            }
	            this.zGridLines_Style = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public float GridLines_Thickness
	    {
	        get
	        {
	            return this.zGridLines_Thickness;
	        }
	        set
	        {
	            this.zGridLines_Thickness = Math.Max(1f, Math.Min(255f, value));
	            this.Invalidate();
	        }
	    }
	
	    [Description("Use this to align the LineNumbers to a chosen corner (or center) within their item-area."), Category("Additional Behavior")]
	    public ContentAlignment LineNrs_Alignment
	    {
	        get
	        {
	            return this.zLineNumbers_Alignment;
	        }
	        set
	        {
	            this.zLineNumbers_Alignment = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Behavior"), Description("Use this to apply Anti-Aliasing to the LineNumbers (high quality). Some fonts will look better without it, though.")]
	    public bool LineNrs_AntiAlias
	    {
	        get
	        {
	            return this.zLineNumbers_AntiAlias;
	        }
	        set
	        {
	            this.zLineNumbers_AntiAlias = value;
	            this.Refresh();
	            this.Invalidate();
	        }
	    }
	
	    [Description("Use this to set whether the LineNumbers should be shown as hexadecimal values."), Category("Additional Behavior")]
	    public bool LineNrs_AsHexadecimal
	    {
	        get
	        {
	            return this.zLineNumbers_ShowAsHexadecimal;
	        }
	        set
	        {
	            this.zLineNumbers_ShowAsHexadecimal = value;
	            this.Refresh();
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Behavior"), Description("Use this to set whether the LineNumbers are allowed to spill out of their item-area, or should be clipped by it.")]
	    public bool LineNrs_ClippedByItemRectangle
	    {
	        get
	        {
	            return this.zLineNumbers_ClipByItemRectangle;
	        }
	        set
	        {
	            this.zLineNumbers_ClipByItemRectangle = value;
	            this.Invalidate();
	        }
	    }
	
	    [Description("Use this to set whether the LineNumbers should have leading zeroes (based on the total amount of textlines)."), Category("Additional Behavior")]
	    public bool LineNrs_LeadingZeroes
	    {
	        get
	        {
	            return this.zLineNumbers_ShowLeadingZeroes;
	        }
	        set
	        {
	            this.zLineNumbers_ShowLeadingZeroes = value;
	            this.Refresh();
	            this.Invalidate();
	        }
	    }
	
	    [Description("Use this property to manually reposition the LineNumbers, relative to their current location."), Category("Additional Behavior")]
	    public Size LineNrs_Offset
	    {
	        get
	        {
	            return this.zLineNumbers_Offset;
	        }
	        set
	        {
	            this.zLineNumbers_Offset = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public Color MarginLines_Color
	    {
	        get
	        {
	            return this.zMarginLines_Color;
	        }
	        set
	        {
	            this.zMarginLines_Color = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public LineNumberDockSide MarginLines_Side
	    {
	        get
	        {
	            return this.zMarginLines_Side;
	        }
	        set
	        {
	            this.zMarginLines_Side = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public DashStyle MarginLines_Style
	    {
	        get
	        {
	            return this.zMarginLines_Style;
	        }
	        set
	        {
	            if (value == DashStyle.Custom)
	            {
	                value = DashStyle.Solid;
	            }
	            this.zMarginLines_Style = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Appearance")]
	    public float MarginLines_Thickness
	    {
	        get
	        {
	            return this.zMarginLines_Thickness;
	        }
	        set
	        {
	            this.zMarginLines_Thickness = Math.Max(1f, Math.Min(255f, value));
	            this.Invalidate();
	        }
	    }
	
	    [Description("Use this property to enable LineNumbers for the chosen RichTextBox."), Category("Add LineNumbers to")]
	    public RichTextBox ParentRichTextBox
	    {
	        get
	        {
	            return this.zParent;
	        }
	        set
	        {
	            this.zParent = value;
	            if (this.zParent != null)
	            {
	                this.Parent = this.zParent.Parent;
	                this.zParent.Refresh();
	            }
	            this.Text = "";
	            this.Refresh();
	            this.Invalidate();
	        }
	    }
	
	    [Description("The BackgroundGradient is a gradual blend of two colors, shown in the back of each LineNumber's item-area."), Category("Additional Behavior")]
	    public bool Show_BackgroundGradient
	    {
	        get
	        {
	            return this.zGradient_Show;
	        }
	        set
	        {
	            this.zGradient_Show = value;
	            this.Invalidate();
	        }
	    }
	
	    [Description("BorderLines are shown on all sides of the LineNumber control."), Category("Additional Behavior")]
	    public bool Show_BorderLines
	    {
	        get
	        {
	            return this.zBorderLines_Show;
	        }
	        set
	        {
	            this.zBorderLines_Show = value;
	            this.Invalidate();
	        }
	    }
	
	    [Description("GridLines are the horizontal divider-lines shown above each LineNumber."), Category("Additional Behavior")]
	    public bool Show_GridLines
	    {
	        get
	        {
	            return this.zGridLines_Show;
	        }
	        set
	        {
	            this.zGridLines_Show = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Behavior")]
	    public bool Show_LineNrs
	    {
	        get
	        {
	            return this.zLineNumbers_Show;
	        }
	        set
	        {
	            this.zLineNumbers_Show = value;
	            this.Invalidate();
	        }
	    }
	
	    [Category("Additional Behavior"), Description("MarginLines are shown on the Left or Right (or both in Height-mode) of the LineNumber control.")]
	    public bool Show_MarginLines
	    {
	        get
	        {
	            return this.zMarginLines_Show;
	        }
	        set
	        {
	            this.zMarginLines_Show = value;
	            this.Invalidate();
	        }
	    }
	
	    [Browsable(false), AmbientValue(""), DefaultValue("")]
	    public override string Text
	    {
	        get
	        {
	            return base.Text;
	        }
	        set
	        {
	            base.Text = "";
	            this.Invalidate();
	        }
	    }
	
	    private RichTextBox zParent
	    {
	        [DebuggerNonUserCode]
	        get
	        {
	            return this._zParent;
	        }
	        [MethodImpl(MethodImplOptions.Synchronized), DebuggerNonUserCode]
	        set
	        {
	            EventHandler handler = new EventHandler(this.zParent_Disposed);
	            ContentsResizedEventHandler handler2 = new ContentsResizedEventHandler(this.zParent_ContentsResized);
	            EventHandler handler3 = new EventHandler(this.zParent_Scroll);
	            EventHandler handler4 = new EventHandler(this.zParent_Scroll);
	            EventHandler handler5 = new EventHandler(this.zParent_Changed);
	            EventHandler handler6 = new EventHandler(this.zParent_Changed);
	            EventHandler handler7 = new EventHandler(this.zParent_Changed);
	            EventHandler handler8 = new EventHandler(this.zParent_Changed);
	            EventHandler handler9 = new EventHandler(this.zParent_Changed);
	            EventHandler handler10 = new EventHandler(this.zParent_Changed);
	            if (this._zParent != null)
	            {
	                this._zParent.Disposed -= handler;
	                this._zParent.ContentsResized -= handler2;
	                this._zParent.VScroll -= handler3;
	                this._zParent.HScroll -= handler4;
	                this._zParent.MultilineChanged -= handler5;
	                this._zParent.TextChanged -= handler6;
	                this._zParent.DockChanged -= handler7;
	                this._zParent.Resize -= handler8;
	                this._zParent.Move -= handler9;
	                this._zParent.LocationChanged -= handler10;
	            }
	            this._zParent = value;
	            if (this._zParent != null)
	            {
	                this._zParent.Disposed += handler;
	                this._zParent.ContentsResized += handler2;
	                this._zParent.VScroll += handler3;
	                this._zParent.HScroll += handler4;
	                this._zParent.MultilineChanged += handler5;
	                this._zParent.TextChanged += handler6;
	                this._zParent.DockChanged += handler7;
	                this._zParent.Resize += handler8;
	                this._zParent.Move += handler9;
	                this._zParent.LocationChanged += handler10;
	            }
	        }
	    }
	
	    private Timer zTimer
	    {
	        [DebuggerNonUserCode]
	        get
	        {
	            return this._zTimer;
	        }
	        [MethodImpl(MethodImplOptions.Synchronized), DebuggerNonUserCode]
	        set
	        {
	            EventHandler handler = new EventHandler(this.zTimer_Tick);
	            if (this._zTimer != null)
	            {
	                this._zTimer.Tick -= handler;
	            }
	            this._zTimer = value;
	            if (this._zTimer != null)
	            {
	                this._zTimer.Tick += handler;
	            }
	        }
	    }
	
	    // Nested Types
	    public enum LineNumberDockSide : byte
	    {
	        Height = 4,
	        Left = 1,
	        None = 0,
	        Right = 2
	    }
	
	    private class LineNumberItem
	    {
	        // Fields
	        internal int LineNumber;
	        internal Rectangle Rectangle;
	
	        // Methods
	        internal LineNumberItem(int zLineNumber, Rectangle zRectangle)
	        {
	            this.LineNumber = zLineNumber;
	            this.Rectangle = zRectangle;
	        }
	    }
	}
	*/
	#endregion
}
