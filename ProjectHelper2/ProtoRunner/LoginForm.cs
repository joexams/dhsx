using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Text;
using System.Windows.Forms;

namespace ProtoRunner
{
    public partial class LoginForm : Form
    {
        public LoginForm()
        {
            InitializeComponent();

            this.DialogResult = System.Windows.Forms.DialogResult.No;
        }

        public string ServerIP
        {
            get { return this.textBox1.Text; }
        }

        public string Port
        {
            get { return this.textBox2.Text; }
        }

        private void loginButton_Click(object sender, EventArgs e)
        {
            this.DialogResult = System.Windows.Forms.DialogResult.OK;
            this.Close();
        }
    }
}
