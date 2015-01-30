using System;

namespace ProtoSpec
{
    public class TestScriptParser
    {
        public TestScriptParser(string code, int lineOffset)
        {
            Code = code;

            LineOffset = lineOffset;

            Scanner = new TestScriptScanner(code);
        }

        private string Code
        {
            get;
            set;
        }

        private int LineOffset
        {
            get;
            set;
        }

        private TestScriptScanner Scanner
        {
            get;
            set;
        }

        private TestScriptToken CurrentToken
        {
            get;
            set;
        }

        private int EnumValue
        {
            get;
            set;
        }


        private TestScriptToken NextToken()
        {
            CurrentToken = Scanner.NextToken();

            return CurrentToken;
        }

        public TestScriptDocument Parse()
        {
            TestScriptStatementList statements = ParseStatements();

            TestScriptDocument document = new TestScriptDocument(statements);

            return document;
        }

        private TestScriptStatementList ParseStatements()
        {
            TestScriptStatementList statements = new TestScriptStatementList();

            NextToken();

            while (CurrentToken.Type != TestScriptTokenType.EndOfFile)
            {
                TestScriptStatement statement = ParseStatement();

                if (statement != null)
                {
                    statements.Add(statement);

                    NextToken();
                }
            }

            return statements;
        }

        private TestScriptStatement ParseStatement()
        {
            TestScriptExpression expression = ParseExpression();

            if (expression != null)
            {
                NextToken();

                if (CurrentToken.Type != TestScriptTokenType.Semicolon)
                    Error("语句必须以“;”作为末尾");

                return new TestScriptStatement(expression);
            }

            return null;
        }

        private TestScriptExpression ParseExpression()
        {
            bool isLeftHandSide;

            TestScriptExpression leftHandSide = ParseUnaryExpression(out isLeftHandSide);

            return ParseExpression(leftHandSide, isLeftHandSide);
        }

        private TestScriptExpression ParseExpression(TestScriptExpression leftHandSide, bool canAssign)
        {
            return leftHandSide;
        }

        private TestScriptExpression ParseUnaryExpression(out bool isLeftHandSide)
        {
            TestScriptExpression expression = null;

            isLeftHandSide = false;

            switch (CurrentToken.Type)
            {
                case TestScriptTokenType.Integer:
                    expression = new TestScriptInteger(long.Parse(CurrentToken.Text));

                    isLeftHandSide = false;
                    break;

                case TestScriptTokenType.String:
                    expression = new TestScriptString(CurrentToken.StringText);

                    isLeftHandSide = false;
                    break;

                case TestScriptTokenType.Identifier:
                    string module = CurrentToken.Text;

                    NextToken();

                    if (CurrentToken.Type != TestScriptTokenType.Colon)
                    {
                        Error("模块名后应该要有“:”");
                    }

                    NextToken();

                    if (CurrentToken.Type != TestScriptTokenType.Identifier)
                    {
                        Error("缺少成员名称");
                    }

                    string name = CurrentToken.Text;

                    NextToken();

                    if (CurrentToken.Type == TestScriptTokenType.LeftParen)
                    {
                        TestScriptExpressionList paramList = new TestScriptExpressionList();

                        do
                        {
                            NextToken();

                            TestScriptExpression param = ParseExpression();

                            if (param != null)
                            {
                                paramList.Add(param);

                                NextToken();
                            }
                        }
                        while (CurrentToken.Type == TestScriptTokenType.Comma);

                        if (CurrentToken.Type != TestScriptTokenType.RightParen)
                        {
                            Error("缺少右括号");
                        }

                        expression = new TestScriptFunctionCall(module, name, paramList);

                        isLeftHandSide = false;
                    }
                    else
                    {
                        expression = new TestScriptEnumValue(module, name);

                        isLeftHandSide = false;
                    }

                    break;
            }

            return expression;
        }

        private void Error(string message)
        {
            throw new Exception("第" + (Scanner.CurrentLine + LineOffset) + "行 : " + message);
        }
    }
}
