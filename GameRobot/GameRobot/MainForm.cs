using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Text;
using System.Windows.Forms;

namespace GameRobot
{
    public partial class MainForm : Form
    {
        public MainForm()
        {
            InitializeComponent();

            flashPlayer1.LoadSwf(Environment.CurrentDirectory + "/LoadRobot.swf");
        }

        private void MainForm_Resize(object sender, EventArgs e)
        {
            flashPlayer1.Left = (panel1.Width - flashPlayer1.Width) / 2;
            flashPlayer1.Top = (panel1.Height - flashPlayer1.Height) / 2;
        }
    }
}
