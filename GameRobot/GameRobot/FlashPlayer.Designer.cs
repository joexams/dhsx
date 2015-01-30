namespace GameRobot
{
    partial class FlashPlayer
    {
        /// <summary> 
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary> 
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Component Designer generated code

        /// <summary> 
        /// Required method for Designer support - do not modify 
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FlashPlayer));
            this.TheFlashPlayer = new AxShockwaveFlashObjects.AxShockwaveFlash();
            ((System.ComponentModel.ISupportInitialize)(this.TheFlashPlayer)).BeginInit();
            this.SuspendLayout();
            // 
            // TheFlashPlayer
            // 
            this.TheFlashPlayer.Dock = System.Windows.Forms.DockStyle.Fill;
            this.TheFlashPlayer.Enabled = true;
            this.TheFlashPlayer.Location = new System.Drawing.Point(0, 0);
            this.TheFlashPlayer.Name = "TheFlashPlayer";
            this.TheFlashPlayer.OcxState = ((System.Windows.Forms.AxHost.State)(resources.GetObject("TheFlashPlayer.OcxState")));
            this.TheFlashPlayer.Size = new System.Drawing.Size(720, 500);
            this.TheFlashPlayer.TabIndex = 0;
            // 
            // FlashPlayer
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 12F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.BackColor = System.Drawing.Color.Black;
            this.Controls.Add(this.TheFlashPlayer);
            this.Name = "FlashPlayer";
            this.Size = new System.Drawing.Size(720, 500);
            ((System.ComponentModel.ISupportInitialize)(this.TheFlashPlayer)).EndInit();
            this.ResumeLayout(false);

        }

        #endregion

        private AxShockwaveFlashObjects.AxShockwaveFlash TheFlashPlayer;
    }
}
