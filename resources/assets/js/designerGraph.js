window.cytoscape = require("cytoscape");
window.cyCanvas = require("cytoscape-canvas");

window.cy = cytoscape({
    container: document.getElementById("graph"),
    style: [
        // the stylesheet for the graph
        {
            selector: ".node",
            style: {
                label: "data(id)",
                shape: "roundrectangle",
                width: "100px",
                height: "100px",
                "background-color": "#0099ff",
                "border-color": " #000000",
                "border-width": "4px",
                "text-halign": "center",
                "text-valign": "center",
                color: "#ffffff",
                "font-size": "24px",
                "text-outline-color": "#000000",
                "text-outline-width": "1px"
            }
        },

        {
            selector: ".edge",
            style: {
                width: 4,
                "line-color": "#000",
                "target-arrow-color": "#ccc",
                "target-arrow-shape": "triangle"
            }
        },

        {
            selector: ".buttonAddLevel",
            style: {
                label: "",
                width: "75px",
                height: "75px",
                "background-color": "#46c637",
                "border-color": "#1f6b17",
                "border-width": "4px",
                "background-image": "/images/plus.svg",
                "background-width": "50%",
                "background-height": "50%"
            }
        },

        {
            selector: ".buttonAddStep",
            style: {
                label: "",
                width: "75px",
                height: "75px",
                "background-color": "#00a5ff",
                "border-color": "#0037ff",
                "border-width": "4px",
                "background-image": "/images/plus.svg",
                "background-width": "50%",
                "background-height": "50%"
            }
        }
    ],

    autoungrabify: true,
    autounselectify: true,

    layout: {
        name: "preset"
    }
});

/**
 * Fires an Event when Cytoscape is ready for interaction. 
 * Replacement of the Vue 'Mounted'-event, since Cytoscape takes longer to load than Vue and 
 * Vue handles Events after being 'mounted'.
 */
cy.ready(function (evt) {
    Event.fire("graphReady");
})

cy.on("tap", "node", function (evt) {
    let ref = evt.target;
    if (ref.hasClass("buttonAddLevel")) {
        let nID = vObj.getAddLevelButtonIndex(ref.id());
        if (nID != -1) vObj.addLevel(nID + 1);
    } else if (ref.hasClass("buttonAddStep")) {
        let nID = vObj.getAddStepButtonIndex(ref.id());
        if (nID != -1) vObj.addStep("Default title", "Default description", nID + 1);
    } else if (ref.hasClass("node")) {
        vObj.prepareModal(ref);
        $("#modalStep").modal();
    }
});

// ============================================================================================= //

//Canvas of background
const bottomLayer = cy.cyCanvas({
    zIndex: -1
});
const canvas = bottomLayer.getCanvas();
const ctx = canvas.getContext("2d");
cy.on("render cyCanvas.resize", function (evt) {
    bottomLayer.resetTransform(ctx);
    bottomLayer.clear(ctx);
    bottomLayer.setTransform(ctx);
    ctx.save();
    for (var i = 0; i < vObj.levels.length; i++) {
        if (i % 2 == 0) ctx.fillStyle = "#e3e7ed";
        else ctx.fillStyle = "#c6cad1";
        let w = vObj.maxStepsPerLevel / 2 * vObj.deltaX;
        ctx.fillRect(-w - 500, i * vObj.deltaY - vObj.deltaY / 2, 2 * w + 1000, vObj.deltaY);
    }
    ctx.restore();
});