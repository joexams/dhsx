using System;
using System.IO;
using System.Text;
using System.Net.Sockets;
using System.Collections;
using System.Collections.ObjectModel;

namespace ProtoSpec
{
    public class TestScriptEvalContext
    {
        public TestScriptEvalContext(ProtoSpecDocument protoSpec, Socket sock)
        {
            ProtoSpec = protoSpec;
            Sock = sock;
        }

        public ProtoSpecDocument ProtoSpec
        {
            get;
            private set;
        }

        public Socket Sock
        {
            get;
            private set;
        }
    }

    public class TestScriptValue
    {
        public TestScriptValue(object value, TestScriptValueType type)
        {
            Value = value;
            Type = type;
        }

        public object Value
        {
            get;
            private set;
        }

        public TestScriptValueType Type
        {
            get;
            private set;
        }

        public string ModuleName
        {
            get;
            private set;
        }

        public string ActionName
        {
            get;
            private set;
        }

        private string m_EnumName;

        public string EnumName
        {
            get { return m_EnumName != null ? m_EnumName : Value.ToString(); }
            private set { m_EnumName = value; }
        }

        public void AddProperty(string name, TestScriptValue value)
        {
            ((Hashtable)this.Value).Add(name, value);
        }

        public TestScriptValue GetProperty(string name)
        {
            return ((Hashtable)this.Value)[name] as TestScriptValue;
        }

        public void AddItem(TestScriptValue value)
        {
            ((TestScriptValueList)this.Value).Add(value);
        }

        public TestScriptValue GetItem(int index)
        {
            return ((TestScriptValueList)this.Value)[index];
        }

        public static TestScriptValue CreateObject()
        {
            return new TestScriptValue(new Hashtable(), TestScriptValueType.Object);
        }

        public static TestScriptValue CreateList()
        {
            return new TestScriptValue(new TestScriptValueList(), TestScriptValueType.List);
        }

        public static TestScriptValue CreateEnum(string module, string name, long value)
        {
            TestScriptValue result = new TestScriptValue(value, TestScriptValueType.Enum);

            result.ModuleName = module;
            result.EnumName = name;

            return result;
        }

        public static TestScriptValue CreateActionResult(string module, string action, TestScriptValue value)
        {
            TestScriptValue result = new TestScriptValue(value, TestScriptValueType.ActionResult);

            result.ModuleName = module;
            result.ActionName = action;

            return result;
        }
    }

    public class TestScriptValueList : Collection<TestScriptValue>
    {

    }

    public enum TestScriptValueType
    {
        Byte, Short, Integer, Long, Enum, String, List, Object, ActionResult
    }

    public class TestScriptEval
    {
        protected static void Error(string message)
        {
            throw new Exception(message);
        }

        public static TestScriptValue Do(TestScriptDocument script, TestScriptEvalContext context)
        {
            Eval(script.Statements, context);

            Socket socket = context.Sock;

            TestScriptValue list = TestScriptValue.CreateList();

            System.Threading.Thread.Sleep(1000);

            while (socket.Available > 0)
            {
                byte[] head = new byte[4];

                int received = 0;

                while (received != 4)
                {
                    received += socket.Receive(head);
                }

                Array.Reverse(head);

                int length = BitConverter.ToInt32(head, 0);

                received = 0;

                byte[] data = new byte[length];

                while (received != length)
                {
                    received += socket.Receive(data, received, length - received, SocketFlags.None);
                }

                ProtoSpecModule recvModule = context.ProtoSpec.Modules.GetById(data[0].ToString());

                if (recvModule == null)
                    Error("数据接收失败，无法找到ID为“" + data[0] + "”的模块");

                ProtoSpecAction recvAction = recvModule.Actions.GetById(data[1].ToString());

                if (recvAction == null)
                    Error("数据接收失败，在模块“" + recvModule.Name + "”中无法找到ID位“" + data[1] + "”的操作");

                if (recvAction.Output.Columns.Count > 0)
                {
                    int parseOffset = 2;

                    TestScriptValue recvValue = TestScriptValue.CreateObject();

                    ParseResponse(recvModule.Name, data, ref parseOffset, recvAction.Output, recvValue);

                    TestScriptValue actionResult = TestScriptValue.CreateActionResult(recvModule.Name, recvAction.Name, recvValue);

                    list.AddItem(actionResult);
                }
            }

            return list;
        }

        private static void Eval(TestScriptStatementList statements, TestScriptEvalContext context)
        {
            foreach (TestScriptStatement statement in statements)
            {
                Eval(statement.Expression, context);
            }
        }

        private static TestScriptValue Eval(TestScriptExpression expression, TestScriptEvalContext context)
        {
            switch (expression.Type)
            {
                case TestScriptExpressionType.String:
                    return new TestScriptValue(((TestScriptString)expression).Text, TestScriptValueType.String);

                case TestScriptExpressionType.Integer:
                    return new TestScriptValue(((TestScriptInteger)expression).Value, TestScriptValueType.Long);

                case TestScriptExpressionType.EnumValue:
                    {
                        TestScriptEnumValue enumExp = (TestScriptEnumValue)expression;

                        ProtoSpecModule module = context.ProtoSpec.Modules.GetByName(enumExp.Module, true);

                        if (module == null)
                            Error("上下文中并不包含模块“" + module + "”的定义");

                        long value = module.EnumValues.GetValueByName(enumExp.Name, true);

                        if (value < 0)
                            Error("模块“" + module + "”中并不包含枚举值“" + enumExp.Name + "”的定义");

                        return TestScriptValue.CreateEnum(enumExp.Module, enumExp.Name, value);
                    }

                case TestScriptExpressionType.FunctionCall:
                    {
                        TestScriptFunctionCall funExp = (TestScriptFunctionCall)expression;

                        ProtoSpecModule module = context.ProtoSpec.Modules.GetByName(funExp.Module, true);

                        if (module == null)
                            Error("上下文中不存在模块“" + module + "”");

                        ProtoSpecAction action = module.Actions.GetByName(funExp.Function, true);

                        if (action == null)
                            Error("模块“" + module + "”中不存在操作“" + funExp.Function + "”");

                        return SendData(context, action, funExp.ParamList);
                    }
            }

            return null;
        }

        private static TestScriptValue Eval(TestScriptExpressionList expList, TestScriptEvalContext context)
        {
            TestScriptValue list = TestScriptValue.CreateList();

            foreach (TestScriptExpression expression in expList)
            {
                list.AddItem(Eval(expression, context));
            }

            return list;
        }

        private static TestScriptValue SendData(TestScriptEvalContext context, ProtoSpecAction action, TestScriptExpressionList paramList)
        {
            Socket socket = context.Sock;

            MemoryStream stream = new MemoryStream();

            stream.WriteByte(0);
            stream.WriteByte(0);
            stream.WriteByte(0);
            stream.WriteByte(0);

            byte moduleId = byte.Parse(action.ParentModule.ModuleId);
            byte actionId = byte.Parse(action.ActionId);

            stream.WriteByte(moduleId);
            stream.WriteByte(actionId);

            int i = 0;

            #region Generate Data

            foreach (ProtoSpecColumn column in action.Input.Columns)
            {
                TestScriptValue param = Eval(paramList[i], context);

                i++;

                switch (column.ColumnType)
                {
                    case ProtoSpecColumnType.Enum:
                        stream.WriteByte((byte)(long)param.Value);
                        break;

                    case ProtoSpecColumnType.Byte:
                        stream.WriteByte((byte)(long)param.Value);
                        break;

                    case ProtoSpecColumnType.Short:
                        {
                            short value = (short)(long)param.Value;

                            byte[] bytes = BitConverter.GetBytes(value);

                            stream.WriteByte(bytes[1]);
                            stream.WriteByte(bytes[0]);
                        }
                        break;

                    case ProtoSpecColumnType.Int:
                        {
                            int value = (int)(long)param.Value;

                            byte[] bytes = BitConverter.GetBytes(value);

                            stream.WriteByte(bytes[3]);
                            stream.WriteByte(bytes[2]);
                            stream.WriteByte(bytes[1]);
                            stream.WriteByte(bytes[0]);
                        }
                        break;

                    case ProtoSpecColumnType.Long:
                        {
                            long value = (long)param.Value;

                            byte[] bytes = BitConverter.GetBytes(value);

                            stream.WriteByte(bytes[7]);
                            stream.WriteByte(bytes[6]);
                            stream.WriteByte(bytes[5]);
                            stream.WriteByte(bytes[4]);
                            stream.WriteByte(bytes[3]);
                            stream.WriteByte(bytes[2]);
                            stream.WriteByte(bytes[1]);
                            stream.WriteByte(bytes[0]);
                        }
                        break;

                    case ProtoSpecColumnType.String:
                        {
                            byte[] bytes = Encoding.UTF8.GetBytes((string)param.Value);

                            short length = (short)bytes.Length;

                            byte[] head = BitConverter.GetBytes(length);

                            stream.WriteByte(head[1]);
                            stream.WriteByte(head[0]);

                            stream.Write(bytes, 0, bytes.Length);
                        }
                        break;
                }
            }

            #endregion

            byte[] buffer = stream.ToArray();

            byte[] packHead = BitConverter.GetBytes(buffer.Length - 4);

            buffer[0] = packHead[3];
            buffer[1] = packHead[2];
            buffer[2] = packHead[1];
            buffer[3] = packHead[0];

            socket.Send(buffer);

            stream.Close();
            stream.Dispose();

            return null;
        }

        private static void ParseResponse(string module, byte[] data, ref int parseOffset, ProtoSpecSubset format, TestScriptValue recvValue)
        {
            foreach (ProtoSpecColumn column in format.Columns)
            {
                switch (column.ColumnType)
                {
                    case ProtoSpecColumnType.Byte:
                        {
                            byte value = data[parseOffset++];

                            recvValue.AddProperty(
                                column.Name,
                                new TestScriptValue(value, TestScriptValueType.Byte)
                            );
                        }
                        break;

                    case ProtoSpecColumnType.Short:
                        {
                            byte[] bytes = new byte[2];

                            bytes[1] = data[parseOffset++];
                            bytes[0] = data[parseOffset++];

                            short value = BitConverter.ToInt16(bytes, 0);

                            recvValue.AddProperty(
                                column.Name,
                                new TestScriptValue(value, TestScriptValueType.Short)
                            );
                        }
                        break;

                    case ProtoSpecColumnType.Int:
                        {
                            byte[] bytes = new byte[4];

                            bytes[3] = data[parseOffset++];
                            bytes[2] = data[parseOffset++];
                            bytes[1] = data[parseOffset++];
                            bytes[0] = data[parseOffset++];

                            int value = BitConverter.ToInt32(bytes, 0);

                            recvValue.AddProperty(
                                column.Name,
                                new TestScriptValue(value, TestScriptValueType.Integer)
                            );
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

                            long value = BitConverter.ToInt64(bytes, 0);

                            recvValue.AddProperty(
                                column.Name,
                                new TestScriptValue(value, TestScriptValueType.Long)
                            );
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

                            recvValue.AddProperty(
                                column.Name,
                                new TestScriptValue(text, TestScriptValueType.String)
                            );
                        }
                        break;

                    case ProtoSpecColumnType.List:
                        {
                            byte[] packhead = new byte[2];

                            packhead[1] = data[parseOffset++];
                            packhead[0] = data[parseOffset++];

                            short len = BitConverter.ToInt16(packhead, 0);

                            TestScriptValue list = TestScriptValue.CreateList();

                            for (int i = 0; i < len; i++)
                            {
                                TestScriptValue item = TestScriptValue.CreateObject();

                                ParseResponse(module, data, ref parseOffset, column.Format, item);

                                list.AddItem(item);
                            }

                            recvValue.AddProperty(column.Name, list);
                        }
                        break;

                    case ProtoSpecColumnType.Enum:
                        {
                            byte value = data[parseOffset++];

                            recvValue.AddProperty(
                                column.Name,
                                TestScriptValue.CreateEnum(module, column.Values.GetNameByValue(value), value)
                            );
                        }
                        break;
                }
            }
        }
    }
}
