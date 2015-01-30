using System;
using System.Data;
using System.Text;
using System.Drawing;
using System.Collections;
using System.Windows.Forms;
using System.ComponentModel;
using System.Collections.Generic;

using WeifenLuo.WinFormsUI.Docking;
using ProtoSpec;
using HelperCore;

namespace ProtoRunner
{
    public partial class TracePanel  : DockPanelControl
    {
        public TracePanel()
        {
            InitializeComponent();

            this.toolStrip.Renderer = new DockPanelStripRenderer();

            this.listView1.SmallImageList = new ImageList();
            this.listView1.SmallImageList.Images.Add(Properties.Resources.arrow_right);
            this.listView1.SmallImageList.Images.Add(Properties.Resources.arrow_left);

            this.listView1.Columns[0].Width = this.listView1.Width;
        }

        private int traceDataId = 0;
        private const int traceDataLen = 128;

        public void NetTraceIn(byte[] packet)
        {
            AddTraceItem(true, packet);
        }

        public void NetTraceOut(byte[] packet)
        {
            AddTraceItem(false, packet);
        }

        private delegate void AddTraceItemCallback(bool isIn, byte[] packet);

        private void AddTraceItem(bool isIn, byte[] packet)
        {
            if (this.listView1.InvokeRequired)
            {
                AddTraceItemCallback callback = new AddTraceItemCallback(AddTraceItem);

                this.Invoke(callback, new object[] { isIn, packet });
            }
            else
            {
                string traceId = traceDataId.ToString("000");

                traceDataId += 1;

                if (this.listView1.Items.Count < traceDataLen)
                {
                    ListViewItem item = this.listView1.Items.Insert(0, new TraceListViewItem(traceId, isIn, packet));
                }
                else
                {
                    for (int i = this.listView1.Items.Count - 1; i > 0; i--)
                    {
                        ((TraceListViewItem)this.listView1.Items[i]).Reset((TraceListViewItem)listView1.Items[i - 1]);
                    }

                    ((TraceListViewItem)this.listView1.Items[0]).Reset(traceId, isIn, packet);
                }
            }
        }

        private void toolStripButton_Clear_Click(object sender, EventArgs e)
        {
            this.listView1.Items.Clear();
        }

        private void listView1_SizeChanged(object sender, EventArgs e)
        {
            this.listView1.Columns[0].Width = this.listView1.Width;
        }

        private void listView1_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (listView1.SelectedItems.Count == 1)
            {
                ((TraceListViewItem)listView1.SelectedItems[0]).ShowDetail(treeView1.Nodes);
            }
        }
    }

    public class TraceListViewItem : ListViewItem
    {
        private static List<ProtoSpecModule> protoSpecModules;

        public static List<ProtoSpecModule> ProtoSpecModules
        {
            get
            {
                if (protoSpecModules == null)
                {
                    protoSpecModules = CodeGenerator.GetModuleList(false);
                }

                return protoSpecModules;
            }
        }

        private bool isIn;
        private byte[] data;
        private string traceId;
        private ProtoSpecModule pModule;
        private ProtoSpecAction pAction;

        public TraceListViewItem(string traceId, bool isIn, byte[] packet)
        {
            Reset(traceId, isIn, packet);
        }

        public void ShowDetail(TreeNodeCollection nodes)
        {
            nodes.Clear();

            TreeNode root = nodes.Add(this.Text);

            int parseOffset = 2;

            this.ParseData(data, ref parseOffset, isIn ? pAction.Input : pAction.Output, root.Nodes);

            root.ExpandAll();
        }

        private void ParseData(byte[] data, ref int parseOffset, ProtoSpecSubset format, TreeNodeCollection nodes)
        {
            foreach (ProtoSpecColumn column in format.Columns)
            {
                string columnName = column.Name + " : " + ColumnTypeToString(column) + " = ";

                switch (column.ColumnType)
                {
                    case ProtoSpecColumnType.Byte:
                        columnName += data[parseOffset++].ToString();
                        break;

                    case ProtoSpecColumnType.Short:
                        {
                            byte[] bytes = new byte[2];

                            bytes[1] = data[parseOffset++];
                            bytes[0] = data[parseOffset++];

                            columnName += BitConverter.ToInt16(bytes, 0).ToString();
                        }
                        break;

                    case ProtoSpecColumnType.Int:
                        {
                            byte[] bytes = new byte[4];

                            bytes[3] = data[parseOffset++];
                            bytes[2] = data[parseOffset++];
                            bytes[1] = data[parseOffset++];
                            bytes[0] = data[parseOffset++];

                            columnName += BitConverter.ToInt32(bytes, 0).ToString();
                        }
                        break;

                    case ProtoSpecColumnType.Long:
                        {
                            byte[] bytes = new byte[8];

                            bytes[7] = data[parseOffset++];
                            bytes[6] = data[parseOffset++];
                            bytes[5] = data[parseOffset++];
                            bytes[4] = data[parseOffset++];
                            bytes[3] = data[parseOffset++];
                            bytes[2] = data[parseOffset++];
                            bytes[1] = data[parseOffset++];
                            bytes[0] = data[parseOffset++];

                            columnName += BitConverter.ToInt64(bytes, 0).ToString();
                        }
                        break;

                    case ProtoSpecColumnType.String:
                        {
                            byte[] packhead = new byte[2];

                            packhead[1] = data[parseOffset++];
                            packhead[0] = data[parseOffset++];

                            short len = BitConverter.ToInt16(packhead, 0);

                            string text = Encoding.UTF8.GetString(data, parseOffset, len);

                            parseOffset += len;

                            columnName += "\"" + text + "\"";
                        }
                        break;

                    case ProtoSpecColumnType.Enum:
                        {
                            byte value = data[parseOffset++];

                            columnName += column.Values.GetNameByValue(value);
                        }
                        break;

                    case ProtoSpecColumnType.List:
                        {
                            byte[] packhead = new byte[2];

                            packhead[1] = data[parseOffset++];
                            packhead[0] = data[parseOffset++];

                            short len = BitConverter.ToInt16(packhead, 0);

                            columnName += len.ToString();

                            TreeNode node = nodes.Add(columnName);

                            for (int i = 0; i < len; i++)
                            {
                                TreeNode subnode = node.Nodes.Add("[" + i + "]");

                                ParseData(data, ref parseOffset, column.Format, subnode.Nodes);
                            }
                        }
                        break;

                    case ProtoSpecColumnType.TypeOf:
                        {
                            TreeNode node = nodes.Add(columnName);

                            ParseData(data, ref parseOffset, column.Format, node.Nodes);
                        }
                        break;
                }

                if (column.ColumnType != ProtoSpecColumnType.List)
                    nodes.Add(columnName);
            }
        }

        private string ColumnTypeToString(ProtoSpecColumn column)
        {
            string columnType = "";

            switch (column.ColumnType)
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
                    if (column.ClassName != null)
                    {
                        if (column.ClassModule != null)
                            columnType = "list<" + column.ClassModule + "." + column.ClassName + ">";
                        else
                            columnType = "list<" + column.ClassName + ">";
                    }
                    else
                    {
                        columnType = "list";
                    }
                    break;

                case ProtoSpecColumnType.Enum:
                    columnType = "enum";
                    break;

                case ProtoSpecColumnType.TypeOf:
                    if (column.ClassModule != null)
                        columnType = "typeof<" + column.ClassModule + "." + column.ClassName + ">";
                    else
                        columnType = "typeof<" + column.ClassName + ">";
                    break;
            }

            return columnType;
        }

        public void Reset(string traceId, bool isIn, byte[] packet)
        {
            this.isIn = isIn;

            this.traceId = traceId;

            string moduleId = packet[0].ToString();
            string actionId = packet[1].ToString();

            foreach (ProtoSpecModule module in ProtoSpecModules)
            {
                if (module.ModuleId == moduleId)
                {
                    ProtoSpecAction action = module.Actions.GetById(actionId);

                    if (action != null)
                    {
                        pModule = module;
                        pAction = action;
                    }
                }
            }

            this.Text = traceId + " " + (pModule != null ? pModule.Name : moduleId.ToString()) + ":" + (pAction != null ? pAction.Name : actionId.ToString());
            this.ImageIndex = isIn ? 0 : 1;

            this.data = packet;
        }

        public void Reset(TraceListViewItem listViewItem)
        {
            Reset(listViewItem.traceId, listViewItem.isIn, listViewItem.data);
        }
    }
}
