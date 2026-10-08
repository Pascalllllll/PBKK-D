using Avalonia;
using System;
using System.Globalization;

namespace StudentRegistrationMVVM;

class Program
{
    [STAThread]
    public static void Main(string[] args)
    {
        // Format tanggal Indonesia (dd/MM/yyyy) untuk CalendarDatePicker, apa pun locale sistemnya.
        var indonesia = new CultureInfo("id-ID");
        CultureInfo.DefaultThreadCurrentCulture = indonesia;
        CultureInfo.DefaultThreadCurrentUICulture = indonesia;
        CultureInfo.CurrentCulture = indonesia;
        CultureInfo.CurrentUICulture = indonesia;

        BuildAvaloniaApp().StartWithClassicDesktopLifetime(args);
    }

    public static AppBuilder BuildAvaloniaApp()
        => AppBuilder.Configure<App>()
            .UsePlatformDetect()
            .WithInterFont()
            .LogToTrace();
}
