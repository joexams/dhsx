using System;

namespace ProtoSpec
{
    class TestScriptScanner
    {
        public TestScriptScanner(string code)
        {
            Code = code;

            CodeEndPos = code.Length;
            CurrentPos = 0;
            CurrentLine = 1;

            m_KeywordTable = new TestScriptKeywordTable();
        }

        public string Code
        {
            get;
            private set;
        }

        public int CurrentPos
        {
            get;
            private set;
        }

        public int CodeEndPos
        {
            get;
            private set;
        }

        public int CurrentLine
        {
            get;
            private set;
        }

        private TestScriptKeywordTable m_KeywordTable;

        public char Peek()
        {
            if (CurrentPos == CodeEndPos)
                return '\0';

            return Code[CurrentPos];
        }

        public TestScriptToken NextToken()
        {
            TestScriptToken token = null;

            char c = IgnoreWhiteSpaceAndComment();

            switch (c)
            {
                case ':':
                    token = new TestScriptToken(TestScriptTokenType.Colon, Code, CurrentPos, 1);
                    CurrentPos += 1;
                    break;

                case '(':
                    token = new TestScriptToken(TestScriptTokenType.LeftParen, Code, CurrentPos, 1);
                    CurrentPos += 1;
                    break;

                case ')':
                    token = new TestScriptToken(TestScriptTokenType.RightParen, Code, CurrentPos, 1);
                    CurrentPos += 1;
                    break;

                case ',':
                    token = new TestScriptToken(TestScriptTokenType.Comma, Code, CurrentPos, 1);
                    CurrentPos += 1;
                    break;

                case ';':
                    token = new TestScriptToken(TestScriptTokenType.Semicolon, Code, CurrentPos, 1);
                    CurrentPos += 1;
                    break;

                case '\0':
                    token = new TestScriptToken(TestScriptTokenType.EndOfFile, Code, CurrentPos, 0);
                    break;

                case '"':
                    {
                        char startChar = Peek();

                        int len = 0;
                        int start = CurrentPos;

                        while (true)
                        {
                            len += 1;

                            CurrentPos += 1;

                            if (Peek() == '\\')
                            {
                                len += 1;

                                CurrentPos += 1;
                            }
                            else if (Peek() == startChar)
                            {
                                len += 1;

                                CurrentPos += 1;

                                break;
                            }
                            else if (Peek() == '\0')
                            {
                                throw new Exception("字符串未结束");
                            }
                        }

                        token = new TestScriptToken(TestScriptTokenType.String, Code, start, len);
                    }
                    break;

                default:
                    if (char.IsDigit(c))
                    {
                        int len = 0;
                        int start = CurrentPos;

                        do
                        {
                            len += 1;

                            CurrentPos += 1;
                        }
                        while (char.IsDigit(Peek()));

                        token = new TestScriptToken(TestScriptTokenType.Integer, Code, start, len);
                    }
                    else if (char.IsLetter(c))
                    {
                        int len = 0;
                        int start = CurrentPos;

                        do
                        {
                            len += 1;

                            CurrentPos += 1;

                            c = Peek();
                        }
                        while (char.IsLetterOrDigit(c) || c == '_');

                        TestScriptTokenType type = m_KeywordTable.Match(Code, start, len);

                        token = new TestScriptToken(type, Code, start, len);
                    }
                    break;
            }

            if (token == null)
                token = new TestScriptToken(TestScriptTokenType.Unknow, Code, CurrentPos, 0);

            return token;
        }

        private char IgnoreWhiteSpaceAndComment()
        {
            char c = Peek();

            while (c == '\r' || c == '\n' || c == '\t' || c == ' ' || c == '/')
            {
                if (c == '/')
                {
                    CurrentPos += 1;

                    if (Peek() == '/')
                    {
                        while (true)
                        {
                            CurrentPos += 1;

                            c = Peek();

                            if (c == '\r' || c == '\n' || c == '\0')
                                break;
                        }
                    }
                    else
                    {
                        CurrentPos -= 1;

                        c = '/';

                        break;
                    }
                }
                else if (c == '\r' || c == '\n')
                {
                    CurrentPos += 1;

                    c = Peek();

                    if (c == '\r' || c == '\n')
                    {
                        CurrentPos += 1;

                        c = Peek();
                    }

                    CurrentLine += 1;
                }
                else
                {
                    CurrentPos += 1;

                    c = Peek();
                }
            }

            return c;
        }
    }

    class TestScriptToken
    {
        public TestScriptToken(TestScriptTokenType type, string code, int startIndex, int length)
        {
            Type = type;

            Code = code;

            StartIndex = startIndex;

            Length = length;
        }

        public TestScriptTokenType Type
        {
            get;
            private set;
        }

        public string Code
        {
            get;
            private set;
        }

        public int StartIndex
        {
            get;
            private set;
        }

        public int Length
        {
            get;
            private set;
        }

        public string Text
        {
            get
            {
                return Code.Substring(StartIndex, Length);
            }
        }

        public string StringText
        {
            get
            {
                return Code.Substring(StartIndex + 1, Length - 2);
            }
        }
    }
}
