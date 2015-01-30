using System;
using System.Collections;
using System.Collections.ObjectModel;

namespace ProtoSpec
{
    public class TestScriptDocument
    {
        public TestScriptDocument(TestScriptStatementList statements)
        {
            Statements = statements;
        }

        public TestScriptStatementList Statements
        {
            get;
            private set;
        }
    }

    public class TestScriptStatement
    {
        public TestScriptStatement(TestScriptExpression expression)
        {
            Expression = expression;
        }

        public TestScriptExpression Expression
        {
            get;
            private set;
        }
    }

    public class TestScriptStatementList : Collection<TestScriptStatement>
    {
    }

    public abstract class TestScriptExpression
    {
        public abstract TestScriptExpressionType Type { get; }
    }

    public class TestScriptExpressionList : Collection<TestScriptExpression>
    {
    }

    public enum TestScriptExpressionType
    {
        String, Integer, EnumValue, FunctionCall
    }

    public class TestScriptString : TestScriptExpression
    {
        public TestScriptString(string text)
        {
            Text = text;
        }

        public override TestScriptExpressionType Type
        {
            get { return TestScriptExpressionType.String; }
        }

        public string Text
        {
            get;
            private set;
        }
    }

    public class TestScriptInteger : TestScriptExpression
    {
        public TestScriptInteger(long value)
        {
            Value = value;
        }

        public override TestScriptExpressionType Type
        {
            get { return TestScriptExpressionType.Integer; }
        }

        public long Value
        {
            get;
            private set;
        }
    }

    public class TestScriptEnumValue : TestScriptExpression
    {
        public TestScriptEnumValue(string module, string name)
        {
            Module = module;
            Name = name;
        }

        public override TestScriptExpressionType Type
        {
            get { return TestScriptExpressionType.EnumValue; }
        }

        public string Module
        {
            get;
            private set;
        }

        public string Name
        {
            get;
            private set;
        }
    }

    public class TestScriptFunctionCall : TestScriptExpression
    {
        public TestScriptFunctionCall(string module, string function, TestScriptExpressionList paramList)
        {
            Module = module;
            Function = function;
            ParamList = paramList;
        }

        public override TestScriptExpressionType Type
        {
            get { return TestScriptExpressionType.FunctionCall; }
        }

        public string Module
        {
            get;
            private set;
        }

        public string Function
        {
            get;
            private set;
        }

        public TestScriptExpressionList ParamList
        {
            get;
            private set;
        }
    }
}
