using System;
using System.Collections.ObjectModel;
using System.Collections.Generic;

namespace ProtoSpec
{
    class TestScriptKeyword
    {
        public TestScriptKeyword(string keyword, TestScriptTokenType tokenType)
        {
            Keyword = keyword;
            TokenType = tokenType;
        }

        public string Keyword
        {
            get;
            private set;
        }

        public TestScriptTokenType TokenType
        {
            get;
            private set;
        }
    }

    class TestScriptKeywordList : Collection<TestScriptKeyword>
    {
        public TestScriptKeywordList(TestScriptKeyword[] keywords)
        {
            foreach (TestScriptKeyword keyword in keywords)
            {
                this.Add(keyword);
            }
        }
    }

    class TestScriptKeywordTable
    {
        public TestScriptKeywordTable()
        {
            m_Table = new Dictionary<char, TestScriptKeywordList>();

            Add(
                new TestScriptKeyword("set", TestScriptTokenType.Set)
            );
        }

        private Dictionary<char, TestScriptKeywordList> m_Table;

        public void Add(params TestScriptKeyword[] keywords)
        {
            m_Table.Add(
                keywords[0].Keyword[0],
                new TestScriptKeywordList(keywords)
            );
        }

        public TestScriptTokenType Match(string code, int startIndex, int length)
        {
            TestScriptKeywordList list = null;

            if (m_Table.TryGetValue(code[startIndex], out list) == false)
                return TestScriptTokenType.Identifier;

            foreach (TestScriptKeyword keyword in list)
            {
                if (keyword.Keyword.Length != length)
                    continue;

                if (code.IndexOf(keyword.Keyword, startIndex) == startIndex)
                    return keyword.TokenType;
            }

            return TestScriptTokenType.Identifier;
        }
    }
}
