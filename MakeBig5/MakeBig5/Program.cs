using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace MakeBig5
{
    class Program
    {
        static void Main(string[] args)
        {
            foreach (string file in Directory.GetFiles("./changes/", "*.php"))
            {
                string content = File.ReadAllText(file);

                File.WriteAllText("./changes-big5/" + Path.GetFileName(file), Simplified2Traditional(content));
            }
        }

        public static string Traditional2Simplified(string str)
        { //繁体转简体 
            return Microsoft.VisualBasic.Strings.StrConv(str, Microsoft.VisualBasic.VbStrConv.SimplifiedChinese, 0);
        }

        public static string Simplified2Traditional(string str)
        { //简体转繁体 
            return Microsoft.VisualBasic.Strings.StrConv(str, Microsoft.VisualBasic.VbStrConv.TraditionalChinese, 0).Replace("斗","鬥");
        }
    }
}
