// assets/controllers/toast-display_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        message: String,
        duration: { type: Number, default: 3000 },
        type: { type: String, default: 'success' }
    }

    static toastQueue = [];
    static maxToasts = 3;

    connect() {
        this.addToQueue();
        this.show();
    }

    addToQueue() {
        // Ajouter à la queue
        this.constructor.toastQueue.push(this.element);

        // Si trop de toasts, supprimer le plus ancien
        if (this.constructor.toastQueue.length > this.constructor.maxToasts) {
            const oldestToast = this.constructor.toastQueue.shift();
            const controller = this.application.getControllerForElementAndIdentifier(
                oldestToast,
                'toast-display'
            );
            if (controller) {
                controller.hide();
            }
        }

        // Repositionner les toasts
        this.repositionToasts();
    }

    repositionToasts() {
        this.constructor.toastQueue.forEach((toast, index) => {
            const offset = index * 70; // 70px entre chaque toast
            toast.style.transform = `translateY(${offset}px)`;
        });
    }

    show() {
        const el = this.element;

        el.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
        el.classList.add('opacity-100', 'scale-100');

        this.timeout = setTimeout(() => this.hide(), this.durationValue);
    }

    hide() {
        const el = this.element;

        el.classList.remove('opacity-100', 'scale-100');
        el.classList.add('opacity-0', 'scale-95');

        // Retirer de la queue
        const index = this.constructor.toastQueue.indexOf(el);
        if (index > -1) {
            this.constructor.toastQueue.splice(index, 1);
        }

        // Repositionner les toasts restants
        this.repositionToasts();

        setTimeout(() => {
            el.remove();
        }, 300);
    }

    disconnect() {
        clearTimeout(this.timeout);
        const index = this.constructor.toastQueue.indexOf(this.element);
        if (index > -1) {
            this.constructor.toastQueue.splice(index, 1);
        }
    }
}
