using System;
using System.Collections.Generic;

namespace ProtoSpec
{
    class ProtoSpecScanner
    {
        public ProtoSpecScanner(string code)
        {
            m_Code = code;

            m_CodeEndPos = code.Length;
            m_CurrentPos = 0;
            m_CurrentLine = 1;

            m_KeywordTable = new ProtoSpecKeywordTable();
        }

        private string m_Code;

        public string Code
        {
            get { return m_Code; }
        }

        private int m_CodeEndPos;
        private int m_CurrentPos;

        public int CurrentPos
        {
            get { return m_CurrentPos; }
            private set { m_CurrentPos = value; }
        }

        private int m_CurrentLine;

        public int CurrentLine
        {
            get { return m_CurrentLine; }
            private set { m_CurrentLine = value; }
        }

        private ProtoSpecKeywordTable m_KeywordTable;

        public char Peek()
        {
            if (m_CurrentPos == m_CodeEndPos)
                return '\0';

            return m_Code[m_CurrentPos];
        }

        public ProtoSpecToken NextToken()
        {
            ProtoSpecToken token = null;

            char c = IgnoreWhiteSpaceAndComment();

            switch (c)
            {
                case ':':
                    token = new ProtoSpecToken(ProtoSpecTokenType.Colon, Code, CurrentPos, 1);
                    CurrentPos += 1;
                    break;

                case '=':
                    token = new ProtoSpecToken(ProtoSpecTokenType.Equal, Code, CurrentPos, 1);
                    CurrentPos += 1;
                    break;

                case '{':
                    token = new ProtoSpecToken(ProtoSpecTokenType.LeftBrace, Code, CurrentPos, 1);

                    CurrentPos += 1;
                    break;

                case '}':
                    token = new ProtoSpecToken(ProtoSpecTokenType.RightBrace, Code, CurrentPos, 1);

                    CurrentPos += 1;
                    break;

                case '<':
                    token = new ProtoSpecToken(ProtoSpecTokenType.LeftAngular, Code, CurrentPos, 1);

                    CurrentPos += 1;
                    break;

                case '>':
                    token = new ProtoSpecToken(ProtoSpecTokenType.RightAngular, Code, CurrentPos, 1);

                    CurrentPos += 1;
                    break;

                case '.':
                    token = new ProtoSpecToken(ProtoSpecTokenType.Dot, Code, CurrentPos, 1);

                    CurrentPos += 1;
                    break;

                case '\0':
                    token = new ProtoSpecToken(ProtoSpecTokenType.EndOfFile, Code, CurrentPos, 0);
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

                        token = new ProtoSpecToken(ProtoSpecTokenType.Numeral, Code, start, len);
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

                        ProtoSpecTokenType type = m_KeywordTable.Match(Code, start, len);

                        token = new ProtoSpecToken(type, Code, start, len);
                    }
                    break;
            }

            if (token == null)
                token = new ProtoSpecToken(ProtoSpecTokenType.Unknow, Code, CurrentPos, 0);

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

    class ProtoSpecToken
    {
        public ProtoSpecToken(ProtoSpecTokenType type, string code, int startIndex, int length)
        {
            m_Type = type;

            m_Code = code;

            m_StartIndex = startIndex;

            m_Length = length;
        }

        private ProtoSpecTokenType m_Type;

        public ProtoSpecTokenType Type
        {
            get { return m_Type; }
        }

        private string m_Code;

        public string Code
        {
            get { return m_Code; }
        }

        private int m_StartIndex;

        public int StartIndex
        {
            get { return m_StartIndex; }
        }

        private int m_Length;

        public int Length
        {
            get { return m_Length; }
        }

        public string Text
        {
            get
            {
                return Code.Substring(StartIndex, Length);
            }
        }
    }

    class ProtoSpecKeywordTable
    {
        public ProtoSpecKeywordTable()
        {
            m_Table = new Dictionary<char, ProtoSpecKeywordList>();

            Add(
                new ProtoSpecKeyword("byte", ProtoSpecTokenType.Byte)
            );

            Add(
                new ProtoSpecKeyword("class", ProtoSpecTokenType.Class)
            );

            Add(
                new ProtoSpecKeyword("enum", ProtoSpecTokenType.Enum)
            );

            Add(
                new ProtoSpecKeyword("in", ProtoSpecTokenType.In),
                new ProtoSpecKeyword("int", ProtoSpecTokenType.Int)
            );

            Add(
                new ProtoSpecKeyword("list", ProtoSpecTokenType.List),
                new ProtoSpecKeyword("long", ProtoSpecTokenType.Long)
            );

            Add(
                new ProtoSpecKeyword("out", ProtoSpecTokenType.Out)
            );

            Add(
                new ProtoSpecKeyword("short", ProtoSpecTokenType.Short),
                new ProtoSpecKeyword("string", ProtoSpecTokenType.String)
            );
        }

        private Dictionary<char, ProtoSpecKeywordList> m_Table;

        public void Add(params ProtoSpecKeyword[] keywords)
        {
            m_Table.Add(
                keywords[0].Keyword[0],
                new ProtoSpecKeywordList(keywords)
            );
        }

        public ProtoSpecTokenType Match(string code, int startIndex, int length)
        {
            ProtoSpecKeywordList list = null;

            if (m_Table.TryGetValue(code[startIndex], out list) == false)
                return ProtoSpecTokenType.Identifier;

            foreach (ProtoSpecKeyword keyword in list)
            {
                if (keyword.Keyword.Length != length)
                    continue;

                if (code.IndexOf(keyword.Keyword, startIndex) == startIndex)
                    return keyword.TokenType;
            }

            return ProtoSpecTokenType.Identifier;
        }
    }
}
