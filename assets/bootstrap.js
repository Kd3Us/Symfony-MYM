import { startStimulusApp } from '@symfony/stimulus-bundle';
import SearchController from './controllers/search_controller.js';

const app = startStimulusApp();
app.register('search', SearchController);