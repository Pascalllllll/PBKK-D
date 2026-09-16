namespace Kalkulator
{
    partial class Form1
    {
        private System.ComponentModel.IContainer components = null;
        private System.Windows.Forms.TextBox txtDisplay;
        private System.Windows.Forms.Label lblHistory;

        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        private void InitializeComponent()
        {
            this.txtDisplay = new System.Windows.Forms.TextBox();
            this.lblHistory = new System.Windows.Forms.Label();
            this.SuspendLayout();

            // 
            // txtDisplay
            // 
            this.txtDisplay.Font = new System.Drawing.Font("Segoe UI", 36F, System.Drawing.FontStyle.Bold);
            this.txtDisplay.Location = new System.Drawing.Point(12, 45);
            this.txtDisplay.Name = "txtDisplay";
            this.txtDisplay.ReadOnly = true;
            this.txtDisplay.Size = new System.Drawing.Size(336, 71);
            this.txtDisplay.TabIndex = 0;
            this.txtDisplay.Text = "0";
            this.txtDisplay.TextAlign = System.Windows.Forms.HorizontalAlignment.Right;

            // 
            // lblHistory
            // 
            this.lblHistory.Location = new System.Drawing.Point(12, 15);
            this.lblHistory.Name = "lblHistory";
            this.lblHistory.Size = new System.Drawing.Size(336, 20);
            this.lblHistory.TabIndex = 1;
            this.lblHistory.TextAlign = System.Drawing.ContentAlignment.MiddleRight;

            // Membangun grid tombol kalkulator
            string[,] buttonLabels = new string[,] 
            {
                { "%", "√", "x²", "⌫" },
                { "C", "÷", "×", "-" },
                { "7", "8", "9", "+" },
                { "4", "5", "6", "=" },
                { "1", "2", "3", "" },
                { "+/-", "0", ".", "" }
            };

            int startX = 12;
            int startY = 130;
            int btnWidth = 80;
            int btnHeight = 60;
            int spacing = 5;

            for (int row = 0; row < 6; row++)
            {
                for (int col = 0; col < 4; col++)
                {
                    string label = buttonLabels[row, col];
                    if (string.IsNullOrEmpty(label)) continue;

                    System.Windows.Forms.Button btn = new System.Windows.Forms.Button();
                    btn.Text = label;
                    btn.Name = "btn" + label;

                    // Mengatur ukuran tombol (khusus tombol "=" dan "+" agar lebih tinggi jika diperlukan, 
                    // tetapi dalam desain ini kita buat grid seragam, dan tombol = kita perpanjang)
                    if (label == "=")
                    {
                        btn.Size = new System.Drawing.Size(btnWidth, (btnHeight * 3) + (spacing * 2));
                        btn.Location = new System.Drawing.Point(startX + (col * (btnWidth + spacing)), startY + (row * (btnHeight + spacing)));
                    }
                    else
                    {
                        btn.Size = new System.Drawing.Size(btnWidth, btnHeight);
                        btn.Location = new System.Drawing.Point(startX + (col * (btnWidth + spacing)), startY + (row * (btnHeight + spacing)));
                    }

                    // Menghubungkan tombol dengan fungsinya (Event Handler)
                    if ("0123456789".Contains(label))
                        btn.Click += new System.EventHandler(this.NumberButton_Click);
                    else if (label == "+" || label == "-" || label == "×" || label == "÷")
                        btn.Click += new System.EventHandler(this.OperatorButton_Click);
                    else if (label == "=")
                        btn.Click += new System.EventHandler(this.btnEquals_Click);
                    else if (label == "C")
                        btn.Click += new System.EventHandler(this.btnClear_Click);
                    else if (label == "⌫")
                        btn.Click += new System.EventHandler(this.btnBackspace_Click);
                    else if (label == ".")
                        btn.Click += new System.EventHandler(this.btnDecimal_Click);
                    else if (label == "x²")
                        btn.Click += new System.EventHandler(this.btnSquare_Click);
                    else if (label == "√")
                        btn.Click += new System.EventHandler(this.btnSquareRoot_Click);
                    else if (label == "%")
                        btn.Click += new System.EventHandler(this.btnPercentage_Click);
                    else if (label == "+/-")
                        btn.Click += new System.EventHandler(this.btnNegate_Click);

                    this.Controls.Add(btn);
                }
            }

            this.Controls.Add(this.lblHistory);
            this.Controls.Add(this.txtDisplay);
            
            this.ClientSize = new System.Drawing.Size(360, 520);
            this.Name = "Form1";
            this.Text = "Kalkulator - Hosea Felix Sanjaya (5025241177)";
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedDialog;
            this.MaximizeBox = false;
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.ResumeLayout(false);
            this.PerformLayout();
        }
    }
}