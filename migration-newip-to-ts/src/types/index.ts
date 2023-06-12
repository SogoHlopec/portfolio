export interface Source {
    id: string;
    name: string;
}

export interface Article {
    source: Source;
    author: string;
    content: string;
    description: string;
    publishedAt: string;
    title: string;
    url: string;
    urlToImage: string;
}

export interface Resp {
    articles: Article[];
    status: string;
    totalResults: number;
}
