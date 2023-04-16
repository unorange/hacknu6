<script setup lang="ts">
import { useScriptTag } from "@vueuse/core";

const { scriptTag, load, unload } = useScriptTag(
  "https://maps.api.2gis.ru/2.0/loader.js?pkg=full",
  (el: HTMLScriptElement) => {
    console.log(el);
  }
);

onMounted(async () => {
  await load();
  console.log("sss");
  //   var map;

  //   console.log(DG);

  //   const map = await DG();
  var locationInfo = document.getElementById("location");

  DG.then(function () {
    const map = DG.map("map", {
      center: [54.98, 82.89],
      zoom: 13,
    });
    const marker = DG.marker([54.981, 82.891], {
      draggable: true,
      interactive: true,
    }).addTo(map);

    marker.on("drag", function (e) {
      var lat = e.target._latlng.lat.toFixed(3),
        lng = e.target._latlng.lng.toFixed(3);

      locationInfo.innerHTML = lat + ", " + lng;
    });

    map.on("click", function (e) {
      //   console.log(e.latlng);
      //   console.log(marker);
      //   marker.setLatLng(e.latlng);
      locationInfo.innerHTML = e.latlng.lat + ", " + e.latlng.lng;

      //   const geocoder = DG.GeocoderObject();

      console.log(map.locate());
    });

    console.log(map);
    // console.log(map.geocoder);
  });

  //   const map = new mapgl.Map('container', {
  //     key: 'Your API access key',
  //     center: [55.31878, 25.23584],
  //     zoom: 13,
  // });
});
</script>

<template>
  Координаты маркера:
  <div id="location">LatLng(54.98, 82.89)</div>
  <div id="map" style="width: 500px; height: 400px"></div>
</template>

<style scoped></style>
