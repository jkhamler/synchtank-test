<template>
  <div class="app">
    <h1>Track Manager</h1>

    <form @submit.prevent="handleSubmit">
      <h2>{{ editingTrack ? 'Edit Track' : 'Add Track' }}</h2>
      <div>
        <label for="name">Track Name:</label>
        <input id="name" v-model="form.title" required/>
      </div>
      <div>
        <label for="artist">Artist:</label>
        <input id="artist" v-model="form.artist" required/>
      </div>
      <div>
        <label for="duration">Duration:</label>
        <input id="duration" v-model="form.duration" required/>
      </div>
      <div>
        <label for="isrc">ISRC:</label>
        <input id="isrc" v-model="form.isrc" required/>
      </div>
      <button type="submit">{{ editingTrack ? 'Update' : 'Add' }} Track</button>
      <button type="button" v-if="editingTrack" @click="cancelEdit">Cancel</button>
    </form>

    <hr/>

    <h2>Track List</h2>
    <table border="1" cellpadding="8">
      <thead>
      <tr>
        <th>Track Title</th>
        <th>Artist</th>
        <th>Duration</th>
        <th>ISRC</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="track in tracks" :key="track.id">
        <td>{{ track.title }}</td>
        <td>{{ track.artist }}</td>
        <td>{{ track.duration }}</td>
        <td>{{ track.isrc }}</td>
        <td>
          <button @click="editTrack(track)">Edit</button>
          <button @click="deleteTrack(track.id)">Delete</button>
        </td>
      </tr>
      <tr v-if="tracks.length === 0">
        <td colspan="5">No tracks available.</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import {ref, onMounted} from 'vue'
import axios from 'axios'

// Replace with your Symfony API base URL
const API_URL = 'http://localhost:8001/api/tracks'

axios.defaults.headers.common['Content-Type'] = 'application/json'

// Reactive state
const tracks = ref([])
const form = ref({title: '', artist: '', duration: '', isrc: ''})
const editingTrack = ref(null)

// Load all tracks on mount
onMounted(fetchTracks)

async function fetchTracks() {
  try {
    const response = await axios.get(API_URL)
    tracks.value = response.data
  } catch (err) {
    console.error('Error fetching tracks:', err)
  }
}

async function handleSubmit() {
  try {
    if (editingTrack.value) {
      await axios.put(`${API_URL}/${editingTrack.value.id}`, form.value)
    } else {
      await axios.post(API_URL, form.value)
    }
    await fetchTracks()
    resetForm()
  } catch (err) {
    console.error('Error saving track:', err)
  }
}

function editTrack(track) {
  form.value = {title: track.title, artist: track.artist, duration: track.duration, isrc: track.isrc}
  editingTrack.value = track
}

async function deleteTrack(id) {
  try {
    await axios.delete(`${API_URL}/${id}`)
    await fetchTracks()
  } catch (err) {
    console.error('Error deleting track:', err)
  }
}

function cancelEdit() {
  resetForm()
}

function resetForm() {
  form.value = {title: '', artist: '', duration: '', isrc: ''}
  editingTrack.value = null
}
</script>

<style scoped>
.app {
  max-width: 600px;
  margin: 0 auto;
  font-family: sans-serif;
}

form div {
  margin-bottom: 10px;
}

table {
  width: 100%;
  margin-top: 20px;
}
</style>
