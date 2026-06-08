// =============================================
// FICHIER : Program.cs
// RÔLE    : Point d'entrée de l'application.
//           Lance la fenêtre de connexion admin.
// =============================================

using System;
using System.Windows.Forms;
using DevMatesAdmin.Views;

namespace DevMatesAdmin
{
    static class Program
    {
        /// <summary>
        /// Point d'entrée principal. Lance l'application sur LoginForm.
        /// [STAThread] est requis par Windows Forms (thread à appartement unique).
        /// </summary>
        [STAThread]
        static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            // Démarre sur la fenêtre de connexion administrateur
            Application.Run(new LoginForm());
        }
    }
}
