import { IArticle, IResp, ISource } from '../../interfaces/interfaces';
import News from './news/news';
import Sources from './sources/sources';

export class AppView {
    private news: News;
    private sources: Sources;

    constructor() {
        this.news = new News();
        this.sources = new Sources();
    }

    public drawNews(data: IResp) {
        const values: IArticle[] = data.articles ? data.articles : [];
        this.news.draw(values);
    }

    public drawSources(data: IResp) {
        const values: ISource[] = data.sources ? data.sources : [];
        this.sources.draw(values);
    }
}

export default AppView;
