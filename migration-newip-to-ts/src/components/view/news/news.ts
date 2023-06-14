import { IArticle } from '../../../interfaces/interfaces';
import './news.css';

class News {
    draw(data: IArticle[]): void {
        const news: IArticle[] = data.length >= 10 ? data.filter((_item: IArticle, idx: number) => idx < 10) : data;

        const fragment: DocumentFragment = document.createDocumentFragment();
        const newsItemTemp: HTMLTemplateElement | null = document.querySelector('#newsItemTemp');

        news.forEach((item: IArticle, idx: number) => {
            if (newsItemTemp) {
                const newsClone: Node = newsItemTemp.content.cloneNode(true);
                if (newsClone instanceof DocumentFragment) {
                    if (idx % 2) newsClone.querySelector('.news__item')?.classList.add('alt');

                    const newsMetaPhoto: HTMLTemplateElement | null = newsClone.querySelector('.news__meta-photo');
                    if (newsMetaPhoto)
                        newsMetaPhoto.style.backgroundImage = `url(${item.urlToImage || 'img/news_placeholder.jpg'})`;
                    const newsMetaAuthor: HTMLTemplateElement | null = newsClone.querySelector('.news__meta-author');
                    if (newsMetaAuthor) newsMetaAuthor.textContent = item.author || item.source.name;
                    const newsMetaDate: HTMLTemplateElement | null = newsClone.querySelector('.news__meta-date');
                    if (newsMetaDate)
                        newsMetaDate.textContent = item.publishedAt.slice(0, 10).split('-').reverse().join('-');
                    const newsDescTitle: HTMLTemplateElement | null = newsClone.querySelector(
                        '.news__description-title'
                    );
                    if (newsDescTitle) newsDescTitle.textContent = item.title;
                    const newsDescSource: HTMLTemplateElement | null = newsClone.querySelector(
                        '.news__description-source'
                    );
                    if (newsDescSource) newsDescSource.textContent = item.source.name;
                    const newsDescContent: HTMLTemplateElement | null = newsClone.querySelector(
                        '.news__description-content'
                    );
                    if (newsDescContent) newsDescContent.textContent = item.description;
                    const newsReadMore: HTMLTemplateElement | null = newsClone.querySelector('.news__read-more a');
                    newsReadMore?.setAttribute('href', item.url);

                    fragment.append(newsClone);
                }
            }
        });

        const newsElement: HTMLTemplateElement | null = document.querySelector('.news');
        if (newsElement) {
            newsElement.innerHTML = '';
            newsElement.appendChild(fragment);
        }
    }
}

export default News;
