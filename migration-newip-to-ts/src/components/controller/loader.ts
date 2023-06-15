import { IResp, IOptions, Endpoints } from '../../interfaces/interfaces';

class Loader {
    constructor(private baseLink: string, private options: IOptions) {}

    public getResp(
        { endpoint, options }: { endpoint: Endpoints; options?: IOptions },
        callback: (data: IResp) => void = () => {
            console.error('No callback for GET response');
        }
    ): void {
        this.load('GET', endpoint, callback, options);
    }

    private errorHandler(res: Response): Response {
        if (!res.ok) {
            if (res.status === 401 || res.status === 404)
                console.log(`Sorry, but there is ${res.status} error: ${res.statusText}`);
            throw Error(res.statusText);
        }

        return res;
    }

    private makeUrl(options: IOptions, endpoint: Endpoints): string {
        const urlOptions: IOptions = { ...this.options, ...options };
        let url = `${this.baseLink}${endpoint}?`;

        (Object.keys(urlOptions) as (keyof IOptions)[]).forEach((key: keyof IOptions) => {
            url += `${key}=${urlOptions[key]}&`;
        });

        return url.slice(0, -1);
    }

    private load(method: string, endpoint: Endpoints, callback: (data: IResp) => void, options: IOptions = {}): void {
        fetch(this.makeUrl(options, endpoint), { method })
            .then(this.errorHandler)
            .then((res) => res.json())
            .then((data: IResp) => callback(data))
            .catch((err: Error) => console.error(err));
    }
}

export default Loader;
