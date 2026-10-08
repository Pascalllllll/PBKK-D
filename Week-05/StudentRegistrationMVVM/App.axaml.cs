using Avalonia;
using Avalonia.Controls.ApplicationLifetimes;
using Avalonia.Markup.Xaml;
using StudentRegistrationMVVM.Data;
using StudentRegistrationMVVM.ViewModels;
using StudentRegistrationMVVM.Views;

namespace StudentRegistrationMVVM;

public partial class App : Application
{
    public override void Initialize()
    {
        AvaloniaXamlLoader.Load(this);
    }

    public override void OnFrameworkInitializationCompleted()
    {
        if (ApplicationLifetime is IClassicDesktopStyleApplicationLifetime desktop)
        {
            var viewModel = new MainWindowViewModel(new MahasiswaRepository());
            desktop.MainWindow = new MainWindow { DataContext = viewModel };
            viewModel.MuatCommand.Execute(null);
        }

        base.OnFrameworkInitializationCompleted();
    }
}
