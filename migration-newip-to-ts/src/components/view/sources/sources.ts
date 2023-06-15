import { ISource } from '../../../interfaces/interfaces';
import './sources.css';

class Sources {
    public draw(data: ISource[]) {
        const fragment: DocumentFragment = document.createDocumentFragment();
        const sourceItemTemp = document.querySelector<HTMLTemplateElement>('#sourceItemTemp');

        data.forEach((item: ISource) => {
            if (sourceItemTemp) {
                const sourceClone: Node = sourceItemTemp.content.cloneNode(true);
                if (sourceClone instanceof DocumentFragment) {
                    const sourceItemName = sourceClone.querySelector<HTMLTemplateElement>('.source__item-name');
                    if (sourceItemName) sourceItemName.textContent = item.name;

                    sourceClone
                        .querySelector<HTMLTemplateElement>('.source__item')
                        ?.setAttribute('data-source-id', item.id);

                    fragment.append(sourceClone);
                }
            }
        });

        document.querySelector<HTMLTemplateElement>('.sources')?.append(fragment);
    }
}

export default Sources;
