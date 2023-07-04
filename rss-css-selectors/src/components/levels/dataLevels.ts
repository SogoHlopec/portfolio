const dataLevels = [
  {
    level: 1,
    titleTask: "Select the flower pot",
    html: `<pot class="animated"></pot>
    <pot class="animated"></pot>
    `,
    htmlForViewer: `  <div class="table">
    <pot></pot>
    <pot></pot>
  </div>
    `,
    description: {
      selectorName: "Type Selector",
      title: "Select elements by their type",
      syntax: "A",
      hint: "Selects all elements of type 'A'. Type refers to the type of tag, so <div>, <p> and <ul> are all different element types.",
      examples: "div selects all <div> elements. p selects all <p> elements.",
    },
  },
];

export { dataLevels };
