const sharedSwalOptions = {
    customClass: {
        popup: 'bg-white text-gray-800 border border-gray-200 shadow-2xl rounded-2xl',
    },
    allowOutsideClick: () => !Swal.isLoading(),
    allowEscapeKey:    () => !Swal.isLoading(),
};

// Create the Toast mixin
export const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2800,
    timerProgressBar: true,

    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    },

    ...sharedSwalOptions,

    customClass: {
        ...sharedSwalOptions.customClass,
        popup: 'bg-white border border-gray-200 shadow-xl rounded-xl',
        title: 'text-base font-semibold',
        timerProgressBar: 'bg-green-500',
    }
});

// Create the Alert mixin
export const Alert = Swal.mixin({
    toast: false,
    position: 'center',
    showConfirmButton: true,
    confirmButtonColor: '#4f46e5',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Okay',
    padding: '1.5rem 2rem',

    ...sharedSwalOptions,

    customClass: {
        ...sharedSwalOptions.customClass,
        title: 'text-xl font-bold mb-4',
        htmlContainer: 'text-base leading-relaxed',
        confirmButton: 'px-6 py-2.5 text-sm font-semibold rounded-lg',
    }
});

// Convenient short helpers (still useful)
export function success(msg, title = 'Success') {
    return Toast.fire({
        icon: 'success',
        title,
        text: msg
    });
}

export function error(msg, title = 'Error') {
    return Alert.fire({
        icon: 'error',
        title,
        text: msg
    });
}