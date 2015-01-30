using System;

namespace ProtoSpec
{
    enum TestScriptTokenType
    {
        Unknow, EndOfFile,

        Identifier, 
        LeftParen, 
        RightParen, 
        Comment,
        Colon, 
        Comma, 
        Semicolon, 
        Integer, 
        String
    }
}
