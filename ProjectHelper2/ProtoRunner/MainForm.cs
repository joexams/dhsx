using System;
using System.IO;
using System.Data;
using System.Text;
using System.Drawing;
using System.Windows.Forms;
using System.ComponentModel;
using System.Collections.Generic;
using WeifenLuo.WinFormsUI.Docking;
using System.Net.Sockets;
using System.Net;

namespace ProtoRunner
{
    public partial class MainForm : Form
    {
        private ScriptPanel scriptPanel = null;

        private Socket proxySocket;
        private Socket policySocket;

        public MainForm()
        {
            InitializeComponent();

//#if DEBUG
//            string webPath = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\client"));
//#else
//            string webPath =  Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "client"));
//#endif

            //Cassini.Server cassini = new Cassini.Server(8866, "/", webPath);

            //cassini.Start();

            //policySocket = new Socket(AddressFamily.InterNetwork, SocketType.Stream, ProtocolType.Tcp);
            //policySocket.UseOnlyOverlappedIO = true;
            //policySocket.Bind(new IPEndPoint(IPAddress.Parse("0.0.0.0"), 843));
            //policySocket.Listen(1);

            //policySocket.BeginAccept(new AsyncCallback(OnInSocketAcceptForPolicy), null);

            proxySocket = new Socket(AddressFamily.InterNetwork, SocketType.Stream, ProtocolType.Tcp);
            proxySocket.UseOnlyOverlappedIO = true;
            proxySocket.Bind(new IPEndPoint(IPAddress.Parse("0.0.0.0"), 7788));
            proxySocket.Listen(10);
        }

        private void toolStripButton_New_Click(object sender, EventArgs e)
        {
            NewConnect();
        }

        private void NewConnect()
        {
            toolStripButton_New.Enabled = false;
            toolStripButton_Run.Enabled = false;
            toolStripButton_Open.Enabled = false;
            toolStripButton_Save.Enabled = false;

            LoginForm loginForm = new LoginForm();

            if (loginForm.ShowDialog() == System.Windows.Forms.DialogResult.OK)
            {
                try
                {
                    scriptPanel = new ScriptPanel();

                    proxySocket.BeginAccept(new AsyncCallback(OnInSocketAccept), scriptPanel);

                    scriptPanel.VisibleChanged += new EventHandler(scriptPanel_VisibleChanged);

                    scriptPanel.ShowPanel(loginForm.ServerIP, loginForm.Port, dockPanel1);
                }
                catch(Exception ex)
                {
                    MessageBox.Show(ex.Message);
                }
            }
        }

        byte[] dummyBuffer = new byte[512];
        byte[] policyBuffer = Encoding.ASCII.GetBytes(
            "<cross-domain-policy><allow-access-from domain=\"*\" to-ports=\"*\" /></cross-domain-policy>\0"
        );

        //private void OnInSocketAcceptForPolicy(IAsyncResult result)
        //{
        //    try
        //    {
        //        Socket flashSocket = policySocket.EndAccept(result);

        //        policySocket.BeginAccept(new AsyncCallback(OnInSocketAcceptForPolicy), null);

        //        while (flashSocket.Available == 0)
        //        {
        //        }

        //        while (flashSocket.Available != 0)
        //        {
        //            flashSocket.Receive(dummyBuffer);
        //        }

        //        int sendLen = 0;

        //        while (sendLen < policyBuffer.Length)
        //        {
        //            sendLen += flashSocket.Send(policyBuffer, sendLen, policyBuffer.Length - sendLen, SocketFlags.None);
        //        }

        //        flashSocket.Close();
        //    }
        //    catch (Exception ex)
        //    {
        //        MessageBox.Show(ex.Message);
        //    }
        //}

        private void OnInSocketAccept(IAsyncResult result)
        {
            ScriptPanel panel = (ScriptPanel)result.AsyncState;

            Socket inSocket = proxySocket.EndAccept(result);

            panel.SetInSocket(inSocket);

            EnableToolStripButton();
        }

        private delegate void EnableToolStripButtonCallback();

        private void EnableToolStripButton()
        {
            if (this.InvokeRequired)
            {
                EnableToolStripButtonCallback callback = new EnableToolStripButtonCallback(EnableToolStripButton);

                this.Invoke(callback);
            }
            else
            {
                toolStripButton_New.Enabled = true;
                toolStripButton_Run.Enabled = true;
                toolStripButton_Open.Enabled = true;
                toolStripButton_Save.Enabled = true;
            }
        }

        void scriptPanel_VisibleChanged(object sender, EventArgs e)
        {
            ScriptPanel panel = ((ScriptPanel)sender);

            if (panel.Visible)
            {
                try
                {
                    panel.TraceDock.Show(dockPanel1, DockState.DockRight);
                }
                catch
                {
                }
            }
            else
            {
                try
                {
                    panel.TraceDock.Hide();
                }
                catch
                {
                }
            }
        }

        private void MainForm_FormClosed(object sender, FormClosedEventArgs e)
        {
            proxySocket.Close();
        }

        private void toolStripButton_Open_Click(object sender, EventArgs e)
        {
            OpenFileDialog dialog = new OpenFileDialog();

            if (dialog.ShowDialog() == System.Windows.Forms.DialogResult.OK)
            {
                scriptPanel.OpenScript(dialog.FileName);
            }
        }

        private void toolStripButton_Run_Click(object sender, EventArgs e)
        {
            scriptPanel.RunScript();
        }

        private void toolStripButton_Save_Click(object sender, EventArgs e)
        {
            scriptPanel.SaveScript();
        }
    }
}
