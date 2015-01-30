using System;
using System.Collections.Generic;
using System.Text;
using System.IO;
using System.Windows.Forms;

using HelperCore;

namespace ProjectHelper
{
    public class KeywordBuilder
    {
        /// <summary>
        /// 关键字
        /// </summary>
        public static void GenerateKeywordCodes()
        {
            try
            {
#if DEBUG
                string fileOfKeywords = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\tool\\ProjectHelper\\ProjectHelper\\Keywords.txt"); 
                string fileOfErlang = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new\\src\\gen\\keyword.erl");
#else
                string fileOfKeywords = Path.Combine(Environment.CurrentDirectory, "tool\\ProjectHelper\\ProjectHelper\\Keywords.txt");
                string fileOfErlang = Path.Combine(Environment.CurrentDirectory, "server-new\\src\\gen\\keyword.erl");
#endif
                string erlangCodes = KeywordGenerator.BuildErlangCodes("keyword", fileOfKeywords, true, false);

                using (StreamWriter sw = new StreamWriter(fileOfErlang, false))
                {
                    sw.Write(erlangCodes);
                }

                MessageBox.Show("关键字过滤代码生成完毕");
            }
            catch (Exception ex)
            {
                MessageBox.Show("关键字过滤代码生成失败：" + ex.Message);
            }
        }

        /// <summary>
        /// 区域码
        /// </summary>
        public static void GenerateAreaCodes()
        {
            try
            {
#if DEBUG
                string fileOfKeywords = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\tool\\ProjectHelper\\ProjectHelper\\Areas.txt"); 
                string fileOfErlang = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new\\src\\gen\\lib_area.erl");
#else
                string fileOfKeywords = Path.Combine(Environment.CurrentDirectory, "tool\\ProjectHelper\\ProjectHelper\\Areas.txt");
                string fileOfErlang = Path.Combine(Environment.CurrentDirectory, "server-new\\src\\gen\\lib_area.erl");
#endif
                string erlangCodes = KeywordGenerator.BuildErlangCodes("lib_area", fileOfKeywords, false, true);

                using (StreamWriter sw = new StreamWriter(fileOfErlang, false))
                {
                    sw.Write(erlangCodes);
                }

                MessageBox.Show("区域检测代码生成完毕");
            }
            catch (Exception ex)
            {
                MessageBox.Show("区域检测代码生成失败：" + ex.Message);
            }
        }

    }
}
