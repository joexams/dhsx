using System;

namespace ProtoSpec
{
    enum ProtoSpecTokenType
    {
        Unknow, EndOfFile,

        Identifier, 	// [a-zA-Z_]
        LeftBrace, 		// {
        RightBrace, 	// }
        Comment, 		// //
        Numeral, 		// [0-9]
        Equal, 			// =
        Colon,			// :
        In, 			// in
        Out,			// out
        LeftAngular, 	// <
        RightAngular, 	// >
        Class,			// calss
        Dot,			// .

        //Types
        Byte    = 20,
        Short   = 21,
        Int     = 22,
        Long    = 23,
        String  = 24,
        List    = 25,
        Enum    = 26,
        TypeOf  = 27
    }
}
