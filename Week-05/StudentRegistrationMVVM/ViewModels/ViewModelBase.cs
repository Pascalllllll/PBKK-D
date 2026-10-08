using System.Collections.Generic;
using System.ComponentModel;
using System.Runtime.CompilerServices;

namespace StudentRegistrationMVVM.ViewModels;

public abstract class ViewModelBase : INotifyPropertyChanged
{
    public event PropertyChangedEventHandler? PropertyChanged;

    protected void OnPropertyChanged([CallerMemberName] string? nama = null) =>
        PropertyChanged?.Invoke(this, new PropertyChangedEventArgs(nama));

    protected bool SetProperty<T>(ref T field, T value, [CallerMemberName] string? nama = null)
    {
        if (EqualityComparer<T>.Default.Equals(field, value))
            return false;

        field = value;
        OnPropertyChanged(nama);
        return true;
    }
}
