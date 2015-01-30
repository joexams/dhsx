using System;
using System.IO;
using System.Text;
using System.Collections;
using MySql.Data.MySqlClient;
using System.Collections.Generic;
using System.Text.RegularExpressions;

using ProtoSpec;

namespace HelperCore
{
    public static partial class CodeGenerator
	{

        private static Regex s_RegexHeadVersion = new Regex(@"\%\%?\s*.+?\s*(\n|\r)+", RegexOptions.IgnoreCase);

        /// <summary>
        /// 关键字
        /// </summary>
        public static void GenerateKeywordCodes()
        {
            try
            {
#if DEBUG
                string fileOfKeywords = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\tool\\ProjectHelper2\\ProjectHelper2\\KeyWords.txt"); 
                string fileOfErlang = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new\\src\\gen\\keyword.erl");
#else
                string fileOfKeywords = Path.Combine(Environment.CurrentDirectory, "tool\\ProjectHelper2\\ProjectHelper2\\KeyWords.txt");
                string fileOfErlang = Path.Combine(Environment.CurrentDirectory, "server-new\\src\\gen\\keyword.erl");
#endif
				fileOfKeywords = FixPath(fileOfKeywords);
				fileOfErlang = FixPath(fileOfErlang);

                string headVersion = string.Empty;
                using (StreamReader sr = new StreamReader(fileOfKeywords))
                {
                    headVersion = sr.ReadLine();
                }
                string wrottenHeadVersion = string.Empty;
                if (File.Exists(fileOfErlang))
                {
                    using (StreamReader sr = new StreamReader(fileOfErlang))
                    {
                        wrottenHeadVersion = sr.ReadLine();
                    }
                }

                if (string.Compare(headVersion, wrottenHeadVersion, true) == 0)
                {
                    Info("关键字未改变，不需要生成");
                }
                else
                {
                    string erlangCodes = BuildErlangCodes("keyword", fileOfKeywords, true, false);
                    erlangCodes = string.Concat(headVersion, "\n", erlangCodes);

                    using (StreamWriter sw = new StreamWriter(fileOfErlang, false))
                    {
                        sw.Write(erlangCodes);
                    }

                    Info("关键字过滤代码生成完毕");
                }
            }
            catch (Exception ex)
            {
                Error("关键字过滤代码生成失败：" + ex.Message);
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
                string fileOfKeywords = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\tool\\ProjectHelper2\\ProjectHelper2\\Areas.txt"); 
                string fileOfErlang = Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server-new\\src\\gen\\lib_area.erl");
#else
                string fileOfKeywords = Path.Combine(Environment.CurrentDirectory, "tool\\ProjectHelper2\\ProjectHelper2\\Areas.txt");
                string fileOfErlang = Path.Combine(Environment.CurrentDirectory, "server-new\\src\\gen\\lib_area.erl");
#endif
				fileOfErlang = FixPath(fileOfErlang);
				fileOfKeywords = FixPath(fileOfKeywords);

                string headVersion = string.Empty;
                using (StreamReader sr = new StreamReader(fileOfKeywords))
                {
                    headVersion = sr.ReadLine();
                }
                string wrottenHeadVersion = string.Empty;
                if (File.Exists(fileOfErlang))
                {
                    using (StreamReader sr = new StreamReader(fileOfErlang))
                    {
                        wrottenHeadVersion = sr.ReadLine();
                    }
                }

                if (string.Compare(headVersion, wrottenHeadVersion, true) == 0)
                {
                    Info("区域代码未改变，不需要生成");
                }
                else
                {
                    string erlangCodes = BuildErlangCodes("lib_area", fileOfKeywords, false, true);
                    erlangCodes = string.Concat(headVersion, "\n", erlangCodes);

                    using (StreamWriter sw = new StreamWriter(fileOfErlang, false))
                    {
                        sw.Write(erlangCodes);
                    }

                    Info("区域检测代码生成完毕");
                }
            }
            catch (Exception ex)
            {
                Error("区域检测代码生成失败：" + ex.Message);
            }
        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="moduleName">模块名称，一般与生成的erl文件名一样</param>
        /// <param name="fileOfKeywords">词库</param>
        /// <param name="isReplace">是否生成替换关键字方法</param>
        /// <param name="isFullMatch">是否全字匹配</param>
        /// <returns></returns>
        private static string BuildErlangCodes(string moduleName, string fileOfKeywords, bool isReplace, bool isFullMatch)
        {
            string contentOfKeywords;
            using (StreamReader sr = new StreamReader(fileOfKeywords))
            {
                contentOfKeywords = sr.ReadToEnd();
            }
            contentOfKeywords = s_RegexHeadVersion.Replace(contentOfKeywords, string.Empty);

            StringBuilder strbldOfErlangCodes = new StringBuilder(string.Empty);
            strbldOfErlangCodes.Append("-module(" + moduleName + ").\n");
            if (isReplace)
                strbldOfErlangCodes.Append("-export([replace/1, is_match/1]).\n\n");
            else
                strbldOfErlangCodes.Append("-export([is_match/1]).\n\n");

            if (isReplace)
            {
                strbldOfErlangCodes.Append("%% 替换关键字为*, 返回：处理过的字符串\n");
                strbldOfErlangCodes.Append("replace(String) -> replace(String, []).\n");
                strbldOfErlangCodes.Append("replace([], Result) -> lists:reverse(Result);\n");
                strbldOfErlangCodes.Append("replace(String, Result) ->\n");
                strbldOfErlangCodes.Append("[OChar | _] = String,");
                strbldOfErlangCodes.Append("\t{Char, String2} = u8(String),\n");
                strbldOfErlangCodes.Append("\tcase mc(Char, String2) of\n");
                strbldOfErlangCodes.Append("\t\tok -> ok;\n");
                strbldOfErlangCodes.Append("\t\tfs -> \n");
                strbldOfErlangCodes.Append("\t\t\tif (OChar > 64) andalso (OChar < 91) -> replace(String2, append_char(OChar, Result)); true -> replace(String2, append_char(Char, Result)) end;\n");
                strbldOfErlangCodes.Append("\t\t{ok, String3, N} -> NL = lists:seq(1, N), CL = lists:map(fun(_NLItem) -> $* end, NL), replace(String3, [CL | Result])\n");
                strbldOfErlangCodes.Append("\tend.\n\n");
            }

            strbldOfErlangCodes.Append("%% 检查字符串是否存在关键字, 返回：true | false\n");
            if (isFullMatch)
            {
                strbldOfErlangCodes.Append("is_match(String) ->\n");
                strbldOfErlangCodes.Append("\t{Char, String2} = u8(String),\n");
                strbldOfErlangCodes.Append("\tcase mc(Char, String2) of\n");
                strbldOfErlangCodes.Append("\t\tok -> true;\n");
                strbldOfErlangCodes.Append("\t\tfs -> false;\n");
                strbldOfErlangCodes.Append("\t\t{ok, _, N} -> N == length(String)\n");
                strbldOfErlangCodes.Append("\tend.\n\n");
            }
            else
            {
                strbldOfErlangCodes.Append("is_match(String) ->\n");
                strbldOfErlangCodes.Append("ResultString = replace(String),\n");
                strbldOfErlangCodes.Append("ResultString =/= String.\n\n");
            }

            strbldOfErlangCodes.Append("u8([]) ->\n\t{null, []};\n");
            strbldOfErlangCodes.Append("u8([Char1 | String]) ->\n\tu8(Char1, String).\n");
            strbldOfErlangCodes.Append("u8(Char1, String) when Char1 < 16#80 ->\n\t{ttl(Char1), String};\n");
            strbldOfErlangCodes.Append("u8(Char1, String) when Char1 < 16#E0 ->\n\t[Char2 | String2] = String,\n\t{{Char1, Char2}, String2};\n");
            strbldOfErlangCodes.Append("u8(Char1, String) when Char1 < 16#F0 ->\n\t[Char2, Char3 | String2] = String,\n\t{{Char1, Char2, Char3}, String2};\n");
            strbldOfErlangCodes.Append("u8(Char1, String) when Char1 < 16#F8 ->\n\t[Char2, Char3, Char4 | String2] = String,\n\t{{Char1, Char2, Char3, Char4}, String2};\n");
            strbldOfErlangCodes.Append("u8(Char1, String) when Char1 < 16#FC->\n\t[Char2, Char3, Char4, Char5 | String2] = String,\n\t{{Char1, Char2, Char3, Char4, Char5}, String2};\n");
            strbldOfErlangCodes.Append("u8(Char1, String) when Char1 < 16#FE->\n\t[Char2, Char3, Char4, Char5, Char6 | String2] = String,\n\t{{Char1, Char2, Char3, Char4, Char5, Char6}, String2}.\n\n");

            if (isReplace)
            {
                strbldOfErlangCodes.Append("append_char(Char1, String) when is_tuple(Char1) == false ->\n\t[Char1 | String];\n");
                strbldOfErlangCodes.Append("append_char({Char3, Char2, Char1}, String) ->\n\t[Char1, Char2, Char3 | String];\n");
                strbldOfErlangCodes.Append("append_char({Char2, Char1}, String) ->\n\t[Char1, Char2 | String];\n");
                strbldOfErlangCodes.Append("append_char({Char4, Char3, Char2, Char1}, String) ->\n\t[Char1, Char2, Char3, Char4 | String];\n");
                strbldOfErlangCodes.Append("append_char({Char5, Char4, Char3, Char2, Char1}, String) ->\n\t[Char1, Char2, Char3, Char4, Char5 | String];\n");
                strbldOfErlangCodes.Append("append_char({Char6, Char5, Char4, Char3, Char2, Char1}, String) ->\n\t[Char1, Char2, Char3, Char4, Char5, Char6 | String].\n\n");
            }

            strbldOfErlangCodes.Append("ttl(UChar) when (UChar > 64) andalso (UChar < 91) -> UChar + 32;\n");
            strbldOfErlangCodes.Append("ttl(LChar) -> LChar.\n\n");

            Dictionary<int, List<string>> dictOfKeywords = new Dictionary<int, List<string>>();
            string[] keywords = contentOfKeywords.Split(new char[] { '\n', '\r' }, StringSplitOptions.RemoveEmptyEntries);
            foreach (string k in keywords)
            {  
                string keyword = k.ToLower().Trim();
                if (string.IsNullOrEmpty(keyword))
                    continue;
                string firstStr = keyword.Substring(0, 1);
                int charCodeOfFirstStr = getCharCodeOfString(firstStr);

                if (!dictOfKeywords.ContainsKey(charCodeOfFirstStr))
                {
                    List<string> ks = new List<string>();
                    ks.Add(keyword);
                    dictOfKeywords.Add(charCodeOfFirstStr, ks);
                }
                else
                {
                    if (!dictOfKeywords[charCodeOfFirstStr].Contains(keyword))
                    {
                        dictOfKeywords[charCodeOfFirstStr].Add(keyword);
                        dictOfKeywords[charCodeOfFirstStr].Sort();
                    }
                }
            }

            foreach (KeyValuePair<int, List<string>> keywordItem in dictOfKeywords)
            {
                List<string> itemKeywords = keywordItem.Value;
                string keyword = itemKeywords[0];

                string firstStr = keyword.Substring(0, 1);
                int charCodeOfFirstStr = keywordItem.Key;
                byte[] bufferOfFirstStr = getCharBuffer(firstStr);

                strbldOfErlangCodes.Append("mc(");
                if (bufferOfFirstStr.Length > 1)
                {
                    setStringParameter(bufferOfFirstStr, ref strbldOfErlangCodes);
                }
                else
                {
                    strbldOfErlangCodes.Append(charCodeOfFirstStr.ToString());
                }
                strbldOfErlangCodes.Append(", S) ->\n");

                if (keyword.Length > 1)
                {
                    StringBuilder tempCondition = new StringBuilder(string.Empty);

                    build(keyword, itemKeywords, 0, ref tempCondition,  0, true);

                    strbldOfErlangCodes.Append(tempCondition.ToString());
                }
                else
                {
                    strbldOfErlangCodes.Append("\t{ok, S, 1};\n");
                }
            }

            strbldOfErlangCodes.Append("mc(_, _) -> fs.\n\n");

            return strbldOfErlangCodes.ToString();
        }

        private static void build(string keyword, List<string> keywords, int index, ref StringBuilder output, int lastIndex, bool isFalse)
        {
            int lengthOfKeyword = keyword.Length;

            int index2 = index + 1;
            int baseTabNumber = index2 * 2 - 1;

            if (lengthOfKeyword > index2)
            {
                int index3 = index2 + 1;
                //string posStr = keyword.Substring(index2, 1);
                //int charCodeOfPosStr = getCharCodeOfString(posStr);
                //byte[] bufferOfPosStr = getCharBuffer(posStr);

                output.Append(loopTabSignal(baseTabNumber));
                output.Append("{C");
                output.Append(index3.ToString());
                output.Append(", S");
                output.Append(index3.ToString());
                output.Append("} = u8(S");
                output.Append(index2 > 1 ? index2.ToString() : string.Empty);
                output.Append("),\n");

                output.Append(loopTabSignal(baseTabNumber));
                output.Append("case C");
                output.Append(index3.ToString());
                output.Append(" of\n");

                #region

                bool doFirst = false;

                List<string> doneList = new List<string>();
                foreach (string skey in keywords)
                {
                    int lengthOfSkey = skey.Length;
                    if (lengthOfSkey <= index2)
                    {
                        continue;
                    }
                    try
                    {
                        string posSkey = skey.Substring(0, index3);
                        string posSWord = skey.Substring(index2, 1);
                        int charCodeOfSWord = getCharCodeOfString(posSWord);
                        byte[] bufferOfSWord = getCharBuffer(posSWord);

                        if (doneList.Contains(posSkey) && doFirst)
                            continue;
                        else
                        {
                            doneList.Add(posSkey);
                            doFirst = true;

                            output.Append(loopTabSignal(baseTabNumber + 1));
                            if (bufferOfSWord.Length > 1)
                            {
                                setStringParameter(bufferOfSWord, ref output);
                            }
                            else
                            {
                                output.Append(charCodeOfSWord.ToString());
                            }
                            output.Append(" ->\n");

                            if (lengthOfSkey - index3 > 0)
                            {
                                string s1 = skey.Substring(0, index3);
                                List<string> tempKeywords = new List<string>();
                                foreach (string tk in keywords)
                                {
                                    int lengthOfTk = tk.Length;
                                    if (lengthOfTk - index3 > 0)
                                    {
                                        string s2 = tk.Substring(0, index2 + 1);
                                        if (string.Compare(s1, s2, true) == 0)
                                            tempKeywords.Add(tk);
                                    }
                                }

                                build(skey, tempKeywords, index2, ref output, lastIndex, isFalse);
                            }
                            else
                            {
                                if (keywords.Count > 1)
                                {
                                    List<string> tempKeywords = new List<string>();
                                    foreach (string tk in keywords)
                                    {
                                        int lengthOfTk = tk.Length;
                                        if (lengthOfTk - index3 > 0)
                                        {
                                            string s2 = tk.Substring(0, index3);
                                            if (string.Compare(skey, s2, true) == 0)
                                                tempKeywords.Add(tk);
                                        }
                                    }

                                    lastIndex = skey.Length;

                                    build(keywords[1], tempKeywords, index2 - 1, ref output, lastIndex, false);
                                }
                                else
                                {
                                    output.Append(loopTabSignal(baseTabNumber + 2));
                                    output.Append("{ok, S");
                                    output.Append(index3.ToString());
                                    output.Append(", ");
                                    output.Append(index3.ToString());
                                    output.Append("};\n");
                                }
                            }
                        }

                    }
                    catch (Exception ex)
                    {
                        Console.WriteLine(ex.Message);
                        Console.ReadKey();
                    }
                }

                output.Append(loopTabSignal(baseTabNumber + 1));
                output.Append("_ ->\n");
                output.Append(loopTabSignal(baseTabNumber + 2));

                if (isFalse)
                {
                    output.Append("fs\n");
                }
                else
                {
                    output.Append("{ok, S");
                    output.Append(lastIndex.ToString());
                    output.Append(", ");
                    output.Append(lastIndex.ToString());
                    output.Append("}\n");
                }

                output.Append(loopTabSignal(baseTabNumber));
                output.Append("end;\n");

                #endregion
            }

            if (lengthOfKeyword - index2 == 0)
            {
                output.Append(loopTabSignal(baseTabNumber));
                output.Append("_ ->\n");
                output.Append(loopTabSignal(baseTabNumber + 1));
                output.Append("{ok, S");
                output.Append(index2 == 1 ? string.Empty : index2.ToString());
                output.Append(", ");
                output.Append(index2.ToString());
                output.Append("};\n");
            }
        }

        private static string loopTabSignal(int number)
        {
            string tabs = "";
            //for (int i = 0; i < number; i++)
            //{
            //    tabs += "\t";
            //}
            return tabs;
        }

        private static void setStringParameter(byte[] bufferOfFirstStr, ref StringBuilder strbldOfErlangCodes)
        {
            strbldOfErlangCodes.Append("{");
            for (int bi = 0; bi < bufferOfFirstStr.Length; bi++)
            {
                byte b = bufferOfFirstStr[bi];
                strbldOfErlangCodes.Append(b.ToString());
                if (bi < bufferOfFirstStr.Length - 1)
                    strbldOfErlangCodes.Append(", ");
            }
            strbldOfErlangCodes.Append("}");
        }

        private static int getCharCodeOfString(string singleString)
        {
            if (string.IsNullOrEmpty(singleString) || singleString.Length > 1)
                return -1;
            return (int)Convert.ToChar(singleString);
        }

        private static byte[] getCharBuffer(string singleString)
        {
            if (string.IsNullOrEmpty(singleString) || singleString.Length > 1)
                return new byte[0];
            return Encoding.UTF8.GetBytes(singleString);
        }


	}
}

