import { IResp } from '../../interfaces/interfaces';
import AppController from '../controller/controller';
import { AppView } from '../view/appView';

class App {
    private controller: AppController;
    private view: AppView;

    constructor() {
        this.controller = new AppController();
        this.view = new AppView();
    }

    public start() {
        const sources: HTMLTemplateElement | null = document.querySelector('.sources');
        sources?.addEventListener('click', (e: MouseEvent) =>
            this.controller.getNews(e, (data: IResp) => this.view.drawNews(data))
        );
        this.controller.getSources((data: IResp) => this.view.drawSources(data));
    }
}

export default App;
