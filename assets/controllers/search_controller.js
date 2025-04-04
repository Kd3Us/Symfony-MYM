import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'results'];
    static values = {
        url: String
    }

    connect() {
        this.debounce = null;
    }

    search() {
        // Annuler le debounce précédent
        if (this.debounce) {
            clearTimeout(this.debounce);
        }

        const query = this.inputTarget.value.trim();

        // Si la requête est vide, cacher les résultats
        if (query.length === 0) {
            this.hideResults();
            return;
        }

        // Debounce pour éviter trop de requêtes
        this.debounce = setTimeout(() => {
            this.fetchResults(query);
        }, 300);
    }

    async fetchResults(query) {
        try {
            const response = await fetch(`${this.urlValue}?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            this.displayResults(data.users);
        } catch (error) {
            console.error('Erreur de recherche:', error);
            this.hideResults();
        }
    }

    displayResults(users) {
        // Vider les résultats précédents
        this.resultsTarget.innerHTML = '';

        if (users.length === 0) {
            this.resultsTarget.innerHTML = `
                <div class="search-no-results">
                    Aucun utilisateur trouvé
                </div>
            `;
            this.resultsTarget.classList.remove('hidden');
            return;
        }

        // Créer et ajouter les éléments de résultats
        users.forEach(user => {
            const userElement = document.createElement('div');
            userElement.classList.add('search-result-item');
            userElement.innerHTML = `
                <img src="${user.profilePicture || '/path/to/default/avatar.png'}" alt="Profile" class="search-result-avatar">
                <div class="search-result-info">
                    <span class="search-result-username">${user.username}</span>
                    <span class="search-result-email">${user.email}</span>
                </div>
            `;
            
            // Ajout d'un événement de clic pour rediriger vers le profil
            userElement.addEventListener('click', () => {
                window.location.href = `/profile/${user.id}`;
            });

            this.resultsTarget.appendChild(userElement);
        });

        this.resultsTarget.classList.remove('hidden');
    }

    hideResults() {
        this.resultsTarget.classList.add('hidden');
    }

    // Cacher les résultats si on clique en dehors
    handleClickOutside(event) {
        if (!this.element.contains(event.target)) {
            this.hideResults();
        }
    }

    initialize() {
        document.addEventListener('click', this.handleClickOutside.bind(this));
    }

    disconnect() {
        document.removeEventListener('click', this.handleClickOutside.bind(this));
    }
}