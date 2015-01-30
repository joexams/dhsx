using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;

namespace ProtoSpec
{
    class ProtoSpecKeyword
    {
        public ProtoSpecKeyword(string keyword, ProtoSpecTokenType tokenType)
        {
            Keyword = keyword;
            TokenType = tokenType;
        }

        public string Keyword
        {
            get;
            private set;
        }

        public ProtoSpecTokenType TokenType
        {
            get;
            private set;
        }
    }

    class ProtoSpecKeywordList : Collection<ProtoSpecKeyword>
    {
        public ProtoSpecKeywordList(ProtoSpecKeyword[] keywords)
        {
            foreach (ProtoSpecKeyword keyword in keywords)
            {
                this.Add(keyword);
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

            Add(
                new ProtoSpecKeyword("typeof", ProtoSpecTokenType.TypeOf)
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
