/*
 * Created by SharpDevelop.
 * User: BG5SBK
 * Date: 2010/9/9
 * Time: 17:01
 * 
 * To change this template use Tools | Options | Coding | Edit Standard Headers.
 */
using System;
using System.IO;
using System.Diagnostics;
using System.Windows.Forms;
using System.Runtime.InteropServices;
using HelperCore;

namespace ProjectHelper
{
	/// <summary>
	/// Class with program entry point.
	/// </summary>
	internal sealed class Program
	{
        [DllImport( "kernel32.dll" )]
        static extern bool AttachConsole( int dwProcessId );
        
        private const int ATTACH_PARENT_PROCESS = -1;
        
		/// <summary>
		/// Program entry point.
		/// </summary>
		[STAThread]
		private static void Main(string[] args)
		{
            //if (args.Length > 0)
            //{
            //    // redirect console output to parent process;
            //    // must be before any calls to Console.WriteLine()
            //    AttachConsole( ATTACH_PARENT_PROCESS );
				
            //    if (args[0] == "gen_code")
            //    {
            //        CodeGenerator.GenerateFlashCode(true);

            //        //CodeGenerator.GenerateErlangCode(true);
            //        CodeGenerator.GenerateErlangCode2(true);
					
            //        if (args.Length > 1)
            //        {
            //            string port = args.Length  == 6 ? args[5] : "3306";
						
            //            if (DatabaseDeployer.Deploy(args[1], args[2], args[3], args[4], port, true))
            //            {
            //                //CodeGenerator.GenerateErlangDatabaseCode(true, args[1], args[2], args[3], args[4], port);
            //                CodeGenerator.GenerateErlangDatabaseCode2(true, args[1], args[2], args[3], args[4], port);
            //                CodeGenerator.GenerateFlashDatabaseCode(true, args[1], args[2], args[3], args[4], port);
            //            }
            //        }
					
            //        return;
            //    }
            //    else if (args[0] == "quick_test")
            //    {
            //        string port = args.Length  == 6 ? args[5] : "3306";
						
            //        if (
            //            DatabaseDeployer.Deploy(args[1], args[2], args[3], args[4], port, true) &&
            //            //CodeGenerator.GenerateErlangDatabaseCode(true, args[1], args[2], args[3], args[4], port) &&
            //            CodeGenerator.GenerateErlangDatabaseCode2(true, args[1], args[2], args[3], args[4], port) &&
            //            CodeGenerator.GenerateFlashDatabaseCode(true, args[1], args[2], args[3], args[4], port) &&
            //            CodeGenerator.GenerateFlashCode(true) &&
            //            //CodeGenerator.GenerateErlangCode(true)
            //            CodeGenerator.GenerateErlangCode2(true)
            //        )
            //        {
            //            #if DEBUG
            //            string serverDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "..\\..\\..\\..\\..\\server"));
            //            #else
            //            string serverDir = Path.GetFullPath(Path.Combine(Environment.CurrentDirectory, "server"));
            //            #endif
						
            //            Process gatewayProc = null;
            //            Process serverProc =  null;
						
            //            gatewayProc = new Process();
						
            //            gatewayProc.StartInfo.FileName = Path.Combine(serverDir, "start_gateway.bat");
            //            gatewayProc.StartInfo.WorkingDirectory = serverDir;
						
            //            gatewayProc.Start();
						
            //            System.Threading.Thread.Sleep(500);
						
            //            serverProc = new Process();
						
            //            serverProc.StartInfo.FileName = Path.Combine(serverDir, "start_server.bat");
            //            serverProc.StartInfo.WorkingDirectory = serverDir;
						
            //            serverProc.Start();
						
            //            return;
            //        }
            //    }
            //}
			
			Application.EnableVisualStyles();
			Application.SetCompatibleTextRenderingDefault(false);
			Application.Run(new MainForm());
		}
	}
}
