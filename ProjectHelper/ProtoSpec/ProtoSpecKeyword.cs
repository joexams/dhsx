using System;
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
}
