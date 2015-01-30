using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Text;
using System.Windows.Forms;

using WeifenLuo.WinFormsUI.Docking;
using System.Net.Sockets;
using ProtoSpec;
using System.IO;

namespace ProtoRunner
{
    public partial class ScriptPanel : DockContent
    {
        public ScriptPanel()
        {
            InitializeComponent();

            scintilla1.ConfigurationManager.Language = "erlang";
            scintilla1.Margins[0].Width = 40;
        }

        private TestScriptEvalContext evalContext;

        private Socket inSocket;
        private int inSocketRecvLen = 0;
        private int inSocketSendLen = 0;
        private byte[] inSocketPacketHead = new byte[4];
        private byte[] inSocketPacketBody = null;

        private Socket outSocket;
        private int outSocketRecvLen = 0;
        private int outSocketSendLen = 0;
        private byte[] outSocketPacketHead = new byte[4];
        private byte[] outSocketPacketBody = null;

        private string serverIP;

        private TracePanel tracePanel = null;
        private DockContent traceDock = null;

        public DockContent TraceDock
        {
            get { return traceDock; }
        }

        public void ShowPanel(string serverIP, string port, DockPanel dockPanel)
        {
            this.Text = serverIP + ":" + port;

            this.serverIP = serverIP;

            traceDock = new DockContent();
            tracePanel = new TracePanel();

            tracePanel.Dock = DockStyle.Fill;
            traceDock.Controls.Add(tracePanel);
            traceDock.Text = "Network";

            traceDock.FormClosing += new FormClosingEventHandler(traceDock_FormClosing);
            this.FormClosed += new FormClosedEventHandler(FlashPanel_FormClosed);

            this.Show(dockPanel);
            
            Socket socket = new Socket(AddressFamily.InterNetwork, SocketType.Stream, ProtocolType.Tcp);
            socket.UseOnlyOverlappedIO = true;
            socket.Connect("localhost", 7788);

            evalContext = new TestScriptEvalContext(TraceListViewItem.ProtoSpecModules, socket);
        }

        private string scriptFileName;

        public void OpenScript(string filename)
        {
            this.scintilla1.Text = File.ReadAllText(filename);

            scriptFileName = filename;
        }

        public void RunScript()
        {
            TestScriptParser parser = new TestScriptParser(this.scintilla1.Text, 0);

            TestScriptRunner.Run(parser.Parse(), evalContext);
        }

        public void SaveScript()
        {
            File.WriteAllText(scriptFileName, this.scintilla1.Text);
        }

        bool closed = false;

        private delegate void TryCloseCallback();

        private void TryClose()
        {
            if (this.InvokeRequired)
            {
                TryCloseCallback callback = new TryCloseCallback(TryClose);

                this.Invoke(callback);
            }
            else
            {
                this.Close();
            }
        }

        private void traceDock_FormClosing(object sender, FormClosingEventArgs e)
        {
            e.Cancel = !closed;
        }

        private void FlashPanel_FormClosed(object sender, FormClosedEventArgs e)
        {
            try
            {
                closed = true;

                if (outSocket != null) outSocket.Close();
                if (inSocket != null) inSocket.Close();

                traceDock.Close();
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
        }

        #region In

        public void SetInSocket(Socket inSocket)
        {
            try
            {
                this.inSocket = inSocket;

                inSocket.UseOnlyOverlappedIO = true;

                inSocket.BeginReceive(
                    inSocketPacketHead, 0, inSocketPacketHead.Length,
                    SocketFlags.None,
                    new AsyncCallback(OnInSocketReceiveHead), null
                );

                outSocket = new Socket(AddressFamily.InterNetwork, SocketType.Stream, ProtocolType.Tcp);
                outSocket.UseOnlyOverlappedIO = true;
                outSocket.Connect(serverIP, 8888);

                outSocket.BeginReceive(
                    outSocketPacketHead, 0, outSocketPacketHead.Length,
                    SocketFlags.None,
                    new AsyncCallback(OnOutSocketReceiveHead), null
                );
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
        }

        private void OnInSocketReceiveHead(IAsyncResult result)
        {
            if (closed)
                return;
            try
            {
                int recvLen = inSocket.EndReceive(result);

                if (recvLen == 0) { this.TryClose(); return; }

                inSocketRecvLen += recvLen;

                if (inSocketRecvLen == inSocketPacketHead.Length)
                {
                    inSocketRecvLen = 0;

                    Array.Reverse(inSocketPacketHead);

                    int length = BitConverter.ToInt32(inSocketPacketHead, 0);

                    Array.Reverse(inSocketPacketHead);

                    inSocketPacketBody = new byte[length];

                    inSocket.BeginReceive(
                        inSocketPacketBody, 0, inSocketPacketBody.Length,
                        SocketFlags.None,
                        new AsyncCallback(OnInSocketReceiveBody), null
                    );
                }
                else
                {
                    inSocket.BeginReceive(
                        inSocketPacketHead, inSocketRecvLen, inSocketPacketHead.Length - inSocketRecvLen,
                        SocketFlags.None,
                        new AsyncCallback(OnInSocketReceiveHead), null
                    );
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
        }

        private void OnInSocketReceiveBody(IAsyncResult result)
        {
            if (closed)
                return;
            try
            {
                int recvLen = inSocket.EndReceive(result);

                if (recvLen == 0) { this.TryClose(); return; }

                inSocketRecvLen += recvLen;

                if (inSocketRecvLen == inSocketPacketBody.Length)
                {
                    tracePanel.NetTraceIn(inSocketPacketBody);

                    inSocketRecvLen = 0;

                    outSocket.BeginSend(
                        inSocketPacketHead, 0, inSocketPacketHead.Length,
                        SocketFlags.None,
                        new AsyncCallback(OnOutSocketSendHead), null
                    );
                }
                else
                {
                    inSocket.BeginReceive(
                        inSocketPacketBody, inSocketRecvLen, inSocketPacketBody.Length - inSocketRecvLen,
                        SocketFlags.None,
                        new AsyncCallback(OnInSocketReceiveBody), null
                    );
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
        }

        private void OnInSocketSendHead(IAsyncResult result)
        {
            if (closed)
                return;
            try
            {
                int sendLen = inSocket.EndSend(result);

                if (sendLen == 0) { this.TryClose(); return; }

                inSocketSendLen = sendLen;

                if (inSocketSendLen == outSocketPacketHead.Length)
                {
                    inSocketSendLen = 0;

                    inSocket.BeginSend(
                        outSocketPacketBody, 0, outSocketPacketBody.Length,
                        SocketFlags.None,
                        new AsyncCallback(OnInSocketSendBody), null
                    );
                }
                else
                {
                    inSocket.BeginSend(
                        outSocketPacketHead, inSocketSendLen, outSocketPacketHead.Length - inSocketSendLen,
                        SocketFlags.None,
                        new AsyncCallback(OnInSocketSendHead),
                        null
                    );
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
        }

        private void OnInSocketSendBody(IAsyncResult result)
        {
            if (closed)
                return;
            try
            {
                int sendLen = inSocket.EndSend(result);

                if (sendLen == 0) { this.TryClose(); return; }

                inSocketSendLen = sendLen;

                if (inSocketSendLen == outSocketPacketBody.Length)
                {
                    inSocketSendLen = 0;

                    outSocket.BeginReceive(
                        outSocketPacketHead, 0, outSocketPacketHead.Length,
                        SocketFlags.None,
                        new AsyncCallback(OnOutSocketReceiveHead), null
                    );
                }
                else
                {
                    inSocket.BeginSend(
                        outSocketPacketBody, inSocketSendLen, outSocketPacketBody.Length - inSocketSendLen,
                        SocketFlags.None,
                        new AsyncCallback(OnInSocketSendBody),
                        null
                    );
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
        }

        #endregion

        #region Out

        private void OnOutSocketReceiveHead(IAsyncResult result)
        {
            if (closed)
                return;
            try
            {
                int recvLen = outSocket.EndReceive(result);

                if (recvLen == 0) { this.TryClose(); return; }

                outSocketRecvLen += recvLen;

                if (outSocketRecvLen == outSocketPacketHead.Length)
                {
                    outSocketRecvLen = 0;

                    Array.Reverse(outSocketPacketHead);

                    int length = BitConverter.ToInt32(outSocketPacketHead, 0);

                    Array.Reverse(outSocketPacketHead);

                    outSocketPacketBody = new byte[length];

                    outSocket.BeginReceive(
                        outSocketPacketBody, 0, outSocketPacketBody.Length,
                        SocketFlags.None,
                        new AsyncCallback(OnOutSocketReceiveBody), null
                    );
                }
                else
                {
                    outSocket.BeginReceive(
                        outSocketPacketHead, outSocketRecvLen, outSocketPacketHead.Length - outSocketRecvLen,
                        SocketFlags.None,
                        new AsyncCallback(OnOutSocketReceiveHead), null
                    );
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
        }

        private void OnOutSocketReceiveBody(IAsyncResult result)
        {
            if (closed)
                return;
            try
            {
                int recvLen = outSocket.EndReceive(result);

                if (recvLen == 0) { this.TryClose(); return; }

                outSocketRecvLen += recvLen;

                if (outSocketRecvLen == outSocketPacketBody.Length)
                {
                    tracePanel.NetTraceOut(outSocketPacketBody);

                    outSocketRecvLen = 0;

                    inSocket.BeginSend(
                        outSocketPacketHead, 0, outSocketPacketHead.Length,
                        SocketFlags.None,
                        new AsyncCallback(OnInSocketSendHead), null
                    );
                }
                else
                {
                    outSocket.BeginReceive(
                        outSocketPacketBody, outSocketRecvLen, outSocketPacketBody.Length - outSocketRecvLen,
                        SocketFlags.None,
                        new AsyncCallback(OnOutSocketReceiveBody), null
                    );
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
        }

        private void OnOutSocketSendHead(IAsyncResult result)
        {
            if (closed)
                return;
            try
            {
                int sendLen = outSocket.EndSend(result);

                if (sendLen == 0) { this.TryClose(); return; }

                outSocketSendLen = sendLen;

                if (outSocketSendLen == inSocketPacketHead.Length)
                {
                    outSocketSendLen = 0;

                    outSocket.BeginSend(
                        inSocketPacketBody, 0, inSocketPacketBody.Length,
                        SocketFlags.None,
                        new AsyncCallback(OnOutSocketSendBody), null
                    );
                }
                else
                {
                    outSocket.BeginSend(
                        inSocketPacketHead, outSocketSendLen, inSocketPacketHead.Length - outSocketSendLen,
                        SocketFlags.None,
                        new AsyncCallback(OnOutSocketSendHead),
                        null
                    );
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
        }

        private void OnOutSocketSendBody(IAsyncResult result)
        {
            if (closed)
                return;
            try
            {
                int sendLen = outSocket.EndSend(result);

                if (sendLen == 0) { this.TryClose(); return; }

                outSocketSendLen = sendLen;

                if (outSocketSendLen == inSocketPacketBody.Length)
                {
                    outSocketSendLen = 0;

                    inSocket.BeginReceive(
                        inSocketPacketHead, 0, inSocketPacketHead.Length,
                        SocketFlags.None,
                        new AsyncCallback(OnInSocketReceiveHead), null
                    );
                }
                else
                {
                    outSocket.BeginSend(
                        inSocketPacketBody, outSocketSendLen, inSocketPacketBody.Length - outSocketSendLen,
                        SocketFlags.None,
                        new AsyncCallback(OnOutSocketSendBody),
                        null
                    );
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
        }

        #endregion

    }
}
