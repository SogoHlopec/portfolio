import { IArticle } from '../../../interfaces/interfaces';
import './news.css';

class News {
    public draw(data: IArticle[]): void {
        const news: IArticle[] = data.length >= 10 ? data.filter((_item: IArticle, idx: number) => idx < 10) : data;

        const fragment: DocumentFragment = document.createDocumentFragment();
        const newsItemTemp = document.querySelector<HTMLTemplateElement>('#newsItemTemp');

        news.forEach((item: IArticle, idx: number) => {
            if (newsItemTemp) {
                const newsClone: Node = newsItemTemp.content.cloneNode(true);
                if (newsClone instanceof DocumentFragment) {
                    if (idx % 2) newsClone.querySelector<HTMLTemplateElement>('.news__item')?.classList.add('alt');

                    const newsMetaPhoto = newsClone.querySelector<HTMLTemplateElement>('.news__meta-photo');
                    if (newsMetaPhoto)
                        newsMetaPhoto.style.backgroundImage = `url(${item.urlToImage || 'news-placeholder..jpg'})`;

                    const newsMetaAuthor = newsClone.querySelector<HTMLTemplateElement>('.news__meta-author');
                    if (newsMetaAuthor) newsMetaAuthor.textContent = item.author || item.source.name;

                    const newsMetaDate = newsClone.querySelector<HTMLTemplateElement>('.news__meta-date');
                    if (newsMetaDate)
                        newsMetaDate.textContent = item.publishedAt.slice(0, 10).split('-').reverse().join('-');

                    const newsDescTitle = newsClone.querySelector<HTMLTemplateElement>('.news__description-title');
                    if (newsDescTitle) newsDescTitle.textContent = item.title;

                    const newsDescSource = newsClone.querySelector<HTMLTemplateElement>('.news__description-source');
                    if (newsDescSource) newsDescSource.textContent = item.source.name;

                    const newsDescContent = newsClone.querySelector<HTMLTemplateElement>('.news__description-content');
                    if (newsDescContent) newsDescContent.textContent = item.description;

                    newsClone.querySelector<HTMLTemplateElement>('.news__read-more a')?.setAttribute('href', item.url);

                    fragment.append(newsClone);
                }
            }
        });

        const newsElement = document.querySelector<HTMLTemplateElement>('.news');
        if (newsElement) {
            newsElement.innerHTML = '';
            newsElement.appendChild(fragment);
        }
    }
}

export default News;
