import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["spinner", "submit", "overlay", "input"];
    static values = {
        delay: { type: Number, default: 500 },
        initialData: Object
    };

    connect() {
        this.onRequest = this.onRequest.bind(this);
        this.onDone = this.onDone.bind(this);
        this.safetyTimeout = null;
        this.isSubmitting = false;

        // Événements UX LiveComponent
        this.element.addEventListener("live:request", this.onRequest);
        this.element.addEventListener("live:response", this.onDone);
        this.element.addEventListener("live:error", this.onDone);
        this.element.addEventListener("submit", this.onRequest);

        // Initialisation de la logique de changement
        this.initializeFormTracking();
    }

    disconnect() {
        this.element.removeEventListener("live:request", this.onRequest);
        this.element.removeEventListener("live:response", this.onDone);
        this.element.removeEventListener("live:error", this.onDone);
        this.element.removeEventListener("submit", this.onRequest);
        this.clearTimer();
        this.clearSafetyTimeout();
    }

    // === GESTION DU TRACKING DES CHANGEMENTS ===
    initializeFormTracking() {
        // Sauvegarde les valeurs initiales
        this.initialDataValue = {};
        this.inputTargets.forEach(input => {
            this.initialDataValue[input.name] = input.value;
            input.addEventListener('input', this.checkChanges.bind(this));
        });

        // État initial : bouton désactivé
        this.updateButtonState();
    }

    checkChanges() {
        // Ne pas vérifier pendant la soumission
        if (this.isSubmitting) return;

        const hasChanged = this.inputTargets.some(input => {
            return input.value !== this.initialDataValue[input.name];
        });

        this.hasFormChanged = hasChanged;
        this.updateButtonState();
    }

    // === GESTION DE LA SOUMISSION ===
    onRequest() {
        this.isSubmitting = true;
        this.clearTimer();
        this.disableSubmitForLoading();
        this.timer = setTimeout(() => this.show(), this.delayValue);

        // Timeout de sécurité
        this.safetyTimeout = setTimeout(() => {
            console.warn('Safety timeout triggered');
            this.onDone();
        }, 500);
    }

    onDone() {
        this.isSubmitting = false;
        this.clearTimer();
        this.clearSafetyTimeout();
        this.hide();

        // Remettre l'état normal du bouton
        this.restoreSubmitButton();

        // Réactualiser l'état basé sur les changements
        setTimeout(() => {
            this.updateButtonState();
        }, 100);
    }

    // === GESTION DE L'ÉTAT DU BOUTON ===
    updateButtonState() {
        if (this.isSubmitting) return; // Ne pas changer pendant la soumission

        this.submitTargets.forEach(btn => {
            if (this.hasFormChanged) {
                this.enableButton(btn);
            } else {
                this.disableButton(btn);
            }
        });
    }

    disableButton(btn) {
        btn.disabled = true;
        btn.classList.add('btn-disabled');
    }

    enableButton(btn) {
        btn.disabled = false;
        btn.classList.remove('btn-disabled');
    }

    disableSubmitForLoading() {
        this.submitTargets.forEach(btn => {
            btn.dataset._oldText = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('btn-disabled');
            btn.innerHTML = `<span class="loading loading-spinner loading-xs"></span>`;
        });
    }

    restoreSubmitButton() {
        this.submitTargets.forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('btn-disabled');
            if (btn.dataset._oldText) {
                btn.innerHTML = btn.dataset._oldText;
                delete btn.dataset._oldText;
            }
        });
    }

    // === GESTION DES SPINNERS ET OVERLAYS ===
    show() {
        this.element.setAttribute("aria-busy", "true");
        this.spinnerTargets.forEach(el => el.classList.remove("hidden"));
        this.overlayTargets.forEach(el => el.classList.remove("hidden"));
    }

    hide() {
        this.element.removeAttribute("aria-busy");
        this.spinnerTargets.forEach(el => el.classList.add("hidden"));
        this.overlayTargets.forEach(el => el.classList.add("hidden"));
    }

    // === UTILITAIRES ===
    clearTimer() {
        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }
    }

    clearSafetyTimeout() {
        if (this.safetyTimeout) {
            clearTimeout(this.safetyTimeout);
            this.safetyTimeout = null;
        }
    }
}
