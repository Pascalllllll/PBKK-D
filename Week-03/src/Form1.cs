using System;
using System.Drawing;
using System.Windows.Forms;

namespace Kalkulator
{
    public partial class Form1 : Form
    {
        double firstNumber = 0;
        double secondNumber = 0;
        double result = 0;
        string operation = "";
        bool isNewNumber = false;

        public Form1()
        {
            InitializeComponent();
            ApplyBlueTheme();
        }

        private void ApplyBlueTheme()
        {
            this.BackColor = Color.FromArgb(30, 40, 50); 
            
            foreach (Control c in this.Controls)
            {
                if (c is Button btn)
                {
                    btn.FlatStyle = FlatStyle.Flat;
                    btn.FlatAppearance.BorderSize = 0;
                    btn.ForeColor = Color.White;
                    btn.Font = new Font("Segoe UI", 14, FontStyle.Bold);

                    if (btn.Text == "=")
                    {
                        btn.BackColor = Color.FromArgb(0, 120, 215); 
                    }
                    else if (btn.Text == "C" || btn.Text == "⌫")
                    {
                        btn.BackColor = Color.FromArgb(200, 50, 50); 
                    }
                    else if (btn.Text == "+" || btn.Text == "-" || btn.Text == "×" || btn.Text == "÷")
                    {
                        btn.BackColor = Color.FromArgb(40, 90, 150); 
                    }
                    else
                    {
                        btn.BackColor = Color.FromArgb(50, 60, 80); 
                    }
                }
                else if (c is TextBox txt)
                {
                    txt.BackColor = Color.FromArgb(30, 40, 50);
                    txt.ForeColor = Color.White;
                    txt.BorderStyle = BorderStyle.None;
                }
                else if (c is Label lbl)
                {
                    lbl.ForeColor = Color.LightGray;
                }
            }
        }

        private void NumberButton_Click(object? sender, EventArgs e)
        {
            Button button = (Button)sender!;
            if (txtDisplay.Text == "0" || isNewNumber)
            {
                txtDisplay.Text = button.Text;
                isNewNumber = false;
            }
            else
            {
                txtDisplay.Text += button.Text;
            }
        }

        private void OperatorButton_Click(object? sender, EventArgs e)
        {
            Button button = (Button)sender!;
            firstNumber = double.Parse(txtDisplay.Text);
            operation = button.Text;
            lblHistory.Text = firstNumber.ToString() + " " + operation;
            isNewNumber = true;
        }

        private void btnSquare_Click(object? sender, EventArgs e)
        {
            double value = double.Parse(txtDisplay.Text);
            txtDisplay.Text = Math.Pow(value, 2).ToString();
            isNewNumber = true;
        }

        private void btnSquareRoot_Click(object? sender, EventArgs e)
        {
            double value = double.Parse(txtDisplay.Text);
            if (value >= 0)
            {
                txtDisplay.Text = Math.Sqrt(value).ToString();
            }
            else
            {
                MessageBox.Show("Input tidak valid untuk akar kuadrat.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
            isNewNumber = true;
        }

        private void btnPercentage_Click(object? sender, EventArgs e)
        {
            double value = double.Parse(txtDisplay.Text);
            txtDisplay.Text = (value / 100).ToString();
            isNewNumber = true;
        }

        private void btnNegate_Click(object? sender, EventArgs e)
        {
            if (txtDisplay.Text != "0")
            {
                if (txtDisplay.Text.StartsWith("-"))
                    txtDisplay.Text = txtDisplay.Text.Substring(1);
                else
                    txtDisplay.Text = "-" + txtDisplay.Text;
            }
        }

        private void btnEquals_Click(object? sender, EventArgs e)
        {
            try
            {
                secondNumber = double.Parse(txtDisplay.Text);
                switch (operation)
                {
                    case "+": result = firstNumber + secondNumber; break;
                    case "-": result = firstNumber - secondNumber; break;
                    case "×": result = firstNumber * secondNumber; break;
                    case "÷":
                        if (secondNumber == 0) throw new DivideByZeroException("Tidak dapat membagi dengan nol.");
                        result = firstNumber / secondNumber;
                        break;
                }
  
                lblHistory.Text = firstNumber.ToString() + " " + operation + " " + secondNumber.ToString() + " =";
                txtDisplay.Text = result.ToString();
                isNewNumber = true;
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private void btnClear_Click(object? sender, EventArgs e)
        {
            firstNumber = 0; secondNumber = 0; result = 0; operation = "";
            lblHistory.Text = ""; txtDisplay.Text = "0";
            isNewNumber = false;
        }

        private void btnDecimal_Click(object? sender, EventArgs e)
        {
            if (isNewNumber)
            {
                txtDisplay.Text = "0.";
                isNewNumber = false;
            }
            else if (!txtDisplay.Text.Contains("."))
            {
                txtDisplay.Text += ".";
            }
        }

        private void btnBackspace_Click(object? sender, EventArgs e)
        {
            if (isNewNumber) return;
            if (txtDisplay.Text.Length > 1)
                txtDisplay.Text = txtDisplay.Text.Substring(0, txtDisplay.Text.Length - 1);
            else
                txtDisplay.Text = "0";
        }
    }
}
